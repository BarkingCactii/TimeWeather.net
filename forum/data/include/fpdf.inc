<?php
/*******************************************************************************
* Software: FPDF                                                               *
* Version:  1.52                                                               *
* Date:     2003-12-30                                                         *
* Author:   Olivier PLATHEY                                                    *
* License:  Freeware                                                           *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/
/*
	Revised for FUDforum by Ilia Alshanetsky
*/

class FPDF
{
//Private properties
var $page;               //current page number
var $n;                  //current object number
var $offsets;            //array of object offsets
var $buffer;             //buffer holding in-memory PDF
var $pages;              //array containing pages
var $state;              //current document state
var $compress;           //compression flag
var $DefOrientation;     //default orientation
var $k;                  //scale factor (number of points in user unit)
var $fwPt,$fhPt;         //dimensions of page format in points
var $fw,$fh;             //dimensions of page format in user unit
var $wPt,$hPt;           //current dimensions of page in points
var $w,$h;               //current dimensions of page in user unit
var $lMargin;            //left margin
var $tMargin;            //top margin
var $rMargin;            //right margin
var $bMargin;            //page break margin
var $cMargin;            //cell margin
var $x,$y;               //current position in user unit for cell positioning
var $lasth;              //height of last cell printed
var $LineWidth;          //line width in user unit
var $CoreFonts;          //array of standard font names
var $fonts;              //array of used fonts
var $PageLinks;          //array of links in pages
var $links;              //array of internal links
var $FontFamily;         //current font family
var $FontStyle;          //current font style
var $CurrentFont;        //current font info
var $FontSizePt;         //current font size in points
var $FontSize;           //current font size in user unit
var $DrawColor;          //commands for drawing color
var $FillColor;          //commands for filling color
var $TextColor;          //commands for text color
var $ColorFlag;          //indicates whether fill and text colors are different
var $ws;                 //word spacing
var $AutoPageBreak;      //automatic page breaking
var $PageBreakTrigger;   //threshold used to trigger page breaks
var $InFooter;           //flag set when processing footer
var $title;              //title
var $subject;            //subject
var $author;             //author
var $creator;            //creator
var $AliasNbPages='{fnb}';       //alias for total number of pages
var $outlines = array();
var $OutlineRoot;
var $_esp_s = array('\\', ')','(');
var $_esp_d = array('\\\\', '\\)','\\(');
var $fpdf_charwidths = array (
  'courier' => 
  array (
    ' ' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'	' => 600,'
' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,'' => 600,' ' => 600,'!' => 600,'"' => 600,'#' => 600,'$' => 600,'%' => 600,'&' => 600,'\'' => 600,'(' => 600,')' => 600,'*' => 600,'+' => 600,',' => 600,'-' => 600,'.' => 600,'/' => 600,0 => 600,1 => 600,2 => 600,3 => 600,4 => 600,5 => 600,6 => 600,7 => 600,8 => 600,9 => 600,':' => 600,';' => 600,'<' => 600,'=' => 600,'>' => 600,'?' => 600,'@' => 600,'A' => 600,'B' => 600,'C' => 600,'D' => 600,'E' => 600,'F' => 600,'G' => 600,'H' => 600,'I' => 600,'J' => 600,'K' => 600,'L' => 600,'M' => 600,'N' => 600,'O' => 600,'P' => 600,'Q' => 600,'R' => 600,'S' => 600,'T' => 600,'U' => 600,'V' => 600,'W' => 600,'X' => 600,'Y' => 600,'Z' => 600,'[' => 600,'\\' => 600,']' => 600,'^' => 600,'_' => 600,'`' => 600,'a' => 600,'b' => 600,'c' => 600,'d' => 600,'e' => 600,'f' => 600,'g' => 600,'h' => 600,'i' => 600,'j' => 600,'k' => 600,'l' => 600,'m' => 600,'n' => 600,'o' => 600,'p' => 600,'q' => 600,'r' => 600,'s' => 600,'t' => 600,'u' => 600,'v' => 600,'w' => 600,'x' => 600,'y' => 600,'z' => 600,'{' => 600,'|' => 600,'}' => 600,'~' => 600,'' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,'�' => 600,
  ),
  'helvetica' => 
  array (
    ' ' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'	' => 278,'
' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,' ' => 278,'!' => 278,'"' => 355,'#' => 556,'$' => 556,'%' => 889,'&' => 667,'\'' => 191,'(' => 333,')' => 333,'*' => 389,'+' => 584,',' => 278,'-' => 333,'.' => 278,'/' => 278,0 => 556,1 => 556,2 => 556,3 => 556,4 => 556,5 => 556,6 => 556,7 => 556,8 => 556,9 => 556,':' => 278,';' => 278,'<' => 584,'=' => 584,'>' => 584,'?' => 556,'@' => 1015,'A' => 667,'B' => 667,'C' => 722,'D' => 722,'E' => 667,'F' => 611,'G' => 778,'H' => 722,'I' => 278,'J' => 500,'K' => 667,'L' => 556,'M' => 833,'N' => 722,'O' => 778,'P' => 667,'Q' => 778,'R' => 722,'S' => 667,'T' => 611,'U' => 722,'V' => 667,'W' => 944,'X' => 667,'Y' => 667,'Z' => 611,'[' => 278,'\\' => 278,']' => 278,'^' => 469,'_' => 556,'`' => 333,'a' => 556,'b' => 556,'c' => 500,'d' => 556,'e' => 556,'f' => 278,'g' => 556,'h' => 556,'i' => 222,'j' => 222,'k' => 500,'l' => 222,'m' => 833,'n' => 556,'o' => 556,'p' => 556,'q' => 556,'r' => 333,'s' => 500,'t' => 278,'u' => 556,'v' => 500,'w' => 722,'x' => 500,'y' => 500,'z' => 500,'{' => 334,'|' => 260,'}' => 334,'~' => 584,'' => 350,'�' => 556,'�' => 350,'�' => 222,'�' => 556,'�' => 333,'�' => 1000,'�' => 556,'�' => 556,'�' => 333,'�' => 1000,'�' => 667,'�' => 333,'�' => 1000,'�' => 350,'�' => 611,'�' => 350,'�' => 350,'�' => 222,'�' => 222,'�' => 333,'�' => 333,'�' => 350,'�' => 556,'�' => 1000,'�' => 333,'�' => 1000,'�' => 500,'�' => 333,'�' => 944,'�' => 350,'�' => 500,'�' => 667,'�' => 278,'�' => 333,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 260,'�' => 556,'�' => 333,'�' => 737,'�' => 370,'�' => 556,'�' => 584,'�' => 333,'�' => 737,'�' => 333,'�' => 400,'�' => 584,'�' => 333,'�' => 333,'�' => 333,'�' => 556,'�' => 537,'�' => 278,'�' => 333,'�' => 333,'�' => 365,'�' => 556,'�' => 834,'�' => 834,'�' => 834,'�' => 611,'�' => 667,'�' => 667,'�' => 667,'�' => 667,'�' => 667,'�' => 667,'�' => 1000,'�' => 722,'�' => 667,'�' => 667,'�' => 667,'�' => 667,'�' => 278,'�' => 278,'�' => 278,'�' => 278,'�' => 722,'�' => 722,'�' => 778,'�' => 778,'�' => 778,'�' => 778,'�' => 778,'�' => 584,'�' => 778,'�' => 722,'�' => 722,'�' => 722,'�' => 722,'�' => 667,'�' => 667,'�' => 611,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 889,'�' => 500,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 278,'�' => 278,'�' => 278,'�' => 278,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 584,'�' => 611,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 500,'�' => 556,'�' => 500,
  ),
  'helveticaB' => 
  array (
    ' ' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'	' => 278,'
' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,'' => 278,' ' => 278,'!' => 333,'"' => 474,'#' => 556,'$' => 556,'%' => 889,'&' => 722,'\'' => 238,'(' => 333,')' => 333,'*' => 389,'+' => 584,',' => 278,'-' => 333,'.' => 278,'/' => 278,0 => 556,1 => 556,2 => 556,3 => 556,4 => 556,5 => 556,6 => 556,7 => 556,8 => 556,9 => 556,':' => 333,';' => 333,'<' => 584,'=' => 584,'>' => 584,'?' => 611,'@' => 975,'A' => 722,'B' => 722,'C' => 722,'D' => 722,'E' => 667,'F' => 611,'G' => 778,'H' => 722,'I' => 278,'J' => 556,'K' => 722,'L' => 611,'M' => 833,'N' => 722,'O' => 778,'P' => 667,'Q' => 778,'R' => 722,'S' => 667,'T' => 611,'U' => 722,'V' => 667,'W' => 944,'X' => 667,'Y' => 667,'Z' => 611,'[' => 333,'\\' => 278,']' => 333,'^' => 584,'_' => 556,'`' => 333,'a' => 556,'b' => 611,'c' => 556,'d' => 611,'e' => 556,'f' => 333,'g' => 611,'h' => 611,'i' => 278,'j' => 278,'k' => 556,'l' => 278,'m' => 889,'n' => 611,'o' => 611,'p' => 611,'q' => 611,'r' => 389,'s' => 556,'t' => 333,'u' => 611,'v' => 556,'w' => 778,'x' => 556,'y' => 556,'z' => 500,'{' => 389,'|' => 280,'}' => 389,'~' => 584,'' => 350,'�' => 556,'�' => 350,'�' => 278,'�' => 556,'�' => 500,'�' => 1000,'�' => 556,'�' => 556,'�' => 333,'�' => 1000,'�' => 667,'�' => 333,'�' => 1000,'�' => 350,'�' => 611,'�' => 350,'�' => 350,'�' => 278,'�' => 278,'�' => 500,'�' => 500,'�' => 350,'�' => 556,'�' => 1000,'�' => 333,'�' => 1000,'�' => 556,'�' => 333,'�' => 944,'�' => 350,'�' => 500,'�' => 667,'�' => 278,'�' => 333,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 280,'�' => 556,'�' => 333,'�' => 737,'�' => 370,'�' => 556,'�' => 584,'�' => 333,'�' => 737,'�' => 333,'�' => 400,'�' => 584,'�' => 333,'�' => 333,'�' => 333,'�' => 611,'�' => 556,'�' => 278,'�' => 333,'�' => 333,'�' => 365,'�' => 556,'�' => 834,'�' => 834,'�' => 834,'�' => 611,'�' => 722,'�' => 722,'�' => 722,'�' => 722,'�' => 722,'�' => 722,'�' => 1000,'�' => 722,'�' => 667,'�' => 667,'�' => 667,'�' => 667,'�' => 278,'�' => 278,'�' => 278,'�' => 278,'�' => 722,'�' => 722,'�' => 778,'�' => 778,'�' => 778,'�' => 778,'�' => 778,'�' => 584,'�' => 778,'�' => 722,'�' => 722,'�' => 722,'�' => 722,'�' => 667,'�' => 667,'�' => 611,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 889,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 556,'�' => 278,'�' => 278,'�' => 278,'�' => 278,'�' => 611,'�' => 611,'�' => 611,'�' => 611,'�' => 611,'�' => 611,'�' => 611,'�' => 584,'�' => 611,'�' => 611,'�' => 611,'�' => 611,'�' => 611,'�' => 556,'�' => 611,'�' => 556,
  ),
);


/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function FPDF($author, $title, $subject, $page_type='letter', $hmargin=15, $wmargin=15, $orientation='P',$unit='mm')
{
	setlocale(LC_NUMERIC,'C');

	//Initialization of properties
	$this->page=0;
	$this->n=2;
	$this->buffer='';
	$this->pages=array();
	$this->state=0;
	$this->fonts=array();
	$this->links=array();
	$this->InFooter=false;
	$this->lasth=0;
	$this->FontFamily='';
	$this->FontStyle='';
	$this->FontSizePt=12;
	$this->DrawColor='0 G';
	$this->FillColor='0 g';
	$this->TextColor='0 g';
	$this->ColorFlag=false;
	$this->ws=0;

	//Standard fonts
	$this->CoreFonts = array('courier'=>'Courier','helvetica'=>'Helvetica','helveticaB'=>'Helvetica-Bold');

	//Scale factor
	switch ($unit) {
		case 'mm':
		default:
			$this->k = 72/25.4;
			break;
		case 'pt':
			$this->k = 1;
			break;
		case 'cm':
			$this->k = 72/2.54;
			break;
		case 'in':
			$this->k = 72;
			break;
	}

	switch (strtolower($page_type)) {
		case 'a3':
			$format = array(841.89,1190.55);
			break;
		case 'a4':
			$format = array(595.28,841.89);
			break;
		case 'a5':
			$format = array(420.94,595.28);
			break;
		case 'letter':
		default:
			$format = array(612,792);
			break;
		case 'legal':
			$format = array(612,1008);
			break;
		
	}

	$this->fwPt = $format[0];
	$this->fhPt = $format[1];

	$this->fw = $this->fwPt / $this->k;
	$this->fh = $this->fhPt / $this->k;

	//Page orientation
	if ($orientation == 'p' || $orientation == 'P') {
		$this->DefOrientation = 'P';
		$this->wPt = $this->fwPt;
		$this->hPt = $this->fhPt;
	} else {
		$this->DefOrientation = 'L';
		$this->wPt = $this->fhPt;
		$this->hPt = $this->fwPt;
	}
		
	$this->w = $this->wPt/$this->k;
	$this->h = $this->hPt/$this->k;

	$this->lMargin = $this->rMargin = $wmargin;
	$this->tMargin = $hmargin;

	$this->cMargin = (28.35 / $this->k) / 10;
	$this->LineWidth = 0.567 / $this->k;
	$this->AutoPageBreak = true;
	$this->bMargin = $hmargin * 2;
	$this->PageBreakTrigger = $this->h - $this->bMargin - 40;
	$this->compress = extension_loaded('zlib');
	$this->title = $title;
	$this->subject = $subject;	
	$this->author = $this->creator = $author;
}

function AddPage()
{
	//Start a new page
	if (!$this->state) {
		$this->state = 1;
		$this->_out('%PDF-1.3');
	}
	$family=$this->FontFamily;
	$style=$this->FontStyle;
	$size=$this->FontSizePt;
	$lw=$this->LineWidth;
	$dc=$this->DrawColor;
	$fc=$this->FillColor;
	$tc=$this->TextColor;
	$cf=$this->ColorFlag;
	if ($this->page > 0) {
		//Page footer
		$this->InFooter = true;
		$this->Footer();
		$this->InFooter = false;
		//Close page
		$this->state = 1;
	}
	//Start new page
	$this->pages[++$this->page] = '';
	$this->state = 2;
	$this->x = $this->lMargin;
	$this->y = $this->tMargin;
	$this->FontFamily = '';
	//Set line cap style to square
	$this->_out('2 J');
	//Set line width
	$this->LineWidth=$lw;
	$this->_out(sprintf('%.2f w',$lw*$this->k));
	//Set font
	if($family)
		$this->SetFont($family,$style,$size);
	//Set colors
	$this->DrawColor=$dc;
	if($dc!='0 G')
		$this->_out($dc);
	$this->FillColor=$fc;
	if($fc!='0 g')
		$this->_out($fc);
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
	//Page header
	$this->Header();
	//Restore line width
	if($this->LineWidth!=$lw)
	{
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.2f w',$lw*$this->k));
	}
	//Restore font
	if($family)
		$this->SetFont($family,$style,$size);
	//Restore colors
	if($this->DrawColor!=$dc)
	{
		$this->DrawColor=$dc;
		$this->_out($dc);
	}
	if($this->FillColor!=$fc)
	{
		$this->FillColor=$fc;
		$this->_out($fc);
	}
	$this->TextColor=$tc;
	$this->ColorFlag=$cf;
}

function SetDrawColor($r=0,$g=0,$b=0)
{
	//Set color for all stroking operations
	if (!$r && !$g && !$b) {
		$this->DrawColor = '0.000 G';
	} else {
		$this->DrawColor = sprintf('%.3f %.3f %.3f RG', $r/255, $g/255, $b/255);
	}
	if ($this->page > 0) {
		$this->_out($this->DrawColor);
	}
}

function SetFillColor($r=0,$g=0,$b=0)
{
	//Set color for all filling operations
	if (!$r && !$g && !$b) {
		$this->FillColor = '0.000 g';
	} else {
		$this->FillColor = sprintf('%.3f %.3f %.3f rg', $r/255, $g/255, $b/255);
	}
	$this->ColorFlag = $this->FillColor != $this->TextColor;
	if ($this->page > 0) {
		$this->_out($this->FillColor);
	}
}

function SetTextColor($r=0,$g=0,$b=0)
{
	//Set color for text
	if (!$r && !$g && !$b) {
		$this->TextColor = '0.000 g';
	} else {
		$this->TextColor = sprintf('%.3f %.3f %.3f rg', $r/255, $g/255, $b/255);
	}
	$this->ColorFlag = $this->FillColor != $this->TextColor;
}

function GetStringWidth($s)
{
        $s = (string)$s;
	$cw =& $this->CurrentFont['cw'];
	$w = 0;
	$l = strlen($s);
	for ($i = 0; $i < $l; $i++) {
		$w += $cw[$s{$i}];
	}
	return $w * $this->FontSize / 1000;
}

function Line($x1,$y1,$x2,$y2)
{
	//Draw a line
	$this->_out(sprintf('%.2f %.2f m %.2f %.2f l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
}

function Rect($x, $y, $w, $h, $style='S')
{
	if ($style == 'F') {
		$op='f';
	} else if($style == 'FD' or $style == 'DF') {
		$op='B';
	}
	$this->_out(sprintf('%.2f %.2f %.2f %.2f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
}

function SetFont($family, $style='', $size=0)
{
	$family = $family ? strtolower($family) : '';

	$style = strtoupper($style);

	if (!$size) {
		$size = $this->FontSizePt;
	}

	if ($this->FontFamily == $family && $this->FontStyle == $style && $this->FontSizePt == $size) {
		return;
	}

	$fontkey = $family . $style;
	if ($family) {
		if (empty($this->fonts[$fontkey])) {
			$this->fonts[$fontkey] = array('i'=> count($this->fonts) + 1,'type'=>'core','name'=>$this->CoreFonts[$fontkey],'up'=>-100,'ut'=>50,'cw'=>$this->fpdf_charwidths[$fontkey]);
		}
		$this->FontFamily = $family;
		$this->FontStyle = $style;
		$this->CurrentFont =& $this->fonts[$fontkey];
	}

	$this->FontSizePt = $size;
	$this->FontSize = $size / $this->k;
	if ($this->page > 0) {
		$this->_out(sprintf('BT /F%d %.2f Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
	}
}

function Link($x,$y,$w,$h,$link)
{
	//Put a link on the page
	$this->PageLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link);
}

function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
{
	//Output a cell
	$k = $this->k;
	if ($this->y + $h > $this->PageBreakTrigger && !$this->InFooter && $this->AutoPageBreak) {
		//Automatic page break
		$x = $this->x;
		$ws = $this->ws;
		if ($ws > 0) {
			$this->ws = 0;
			$this->_out('0 Tw');
		}
		$this->AddPage();
		$this->x = $x;
		if ($ws > 0) {
			$this->ws = $ws;
			$this->_out(sprintf('%.3f Tw', $ws * $k));
		}
	}
	if (!$w) {
		$w = $this->w - $this->rMargin - $this->x;
	}
	$s = '';
	if ($fill || $border) {
		if ($fill) { 
			$op = $border ? 'B' : 'f';
		} else {
			$op = 'S';
		}
		$s = sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
	}

	if ($txt) {
		if ($this->ColorFlag) {
			$s.='q '.$this->TextColor.' ';
		}

		$s .= sprintf('BT %.2f %.2f Td (%s) Tj ET',($this->x+$this->cMargin)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt));

		if ($this->ColorFlag) {
			$s .= ' Q';
		}

		if ($link) {
			$this->Link($this->x+$this->cMargin,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
		}
	}
	if ($s) {
		$this->_out($s);
	}
	$this->lasth = $h;
	if ($ln > 0) {
		//Go to next line
		$this->y += $h;
		if ($ln == 1) {
			$this->x = $this->lMargin;
		}
	} else {
		$this->x += $w;
	}
}

function Write($h,$txt,$link='')
{
	//Output text in flowing mode
	$cw=&$this->CurrentFont['cw'];
	$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s{$i};
		if($c=="\n")
		{
			//Explicit line break
			$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			if($nl==1)
			{
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			}
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($this->x>$this->lMargin)
				{
					//Move to next line
					$this->x=$this->lMargin;
					$this->y+=$h;
					$w=$this->w-$this->rMargin-$this->x;
					$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
					$i++;
					$nl++;
					continue;
				}
				if($i==$j)
					$i++;
				$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',0,$link);
			}
			else
			{
				$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',0,$link);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			if($nl==1)
			{
				$this->x=$this->lMargin;
				$w=$this->w-$this->rMargin-$this->x;
				$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
			}
			$nl++;
		}
		else
			$i++;
	}
	//Last chunk
	if($i!=$j)
		$this->Cell($l/1000*$this->FontSize,$h,substr($s,$j),0,0,'',0,$link);
}

function Ln($h)
{
	$this->x  = $this->lMargin;
	$this->y += $h;
}

function Output($name)
{
	if ($this->state < 3) {
		if (!$this->page) {
			$this->AddPage();
		}
		//Page footer
		$this->InFooter = true;
		$this->Footer();
		$this->InFooter = false;
		//Close page
		$this->state = 1;
		//Close document
		$this->_enddoc();
	}

	if ($_SERVER["SERVER_PORT"] == "443" && (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') !== false)) {
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0", 1);
		header("Pragma: public", 1);
	}

	header('Content-Type: application/pdf');
	header('Content-Length: '.strlen($this->buffer));
	header('Content-disposition: inline; filename='.$name);

	echo $this->buffer;
}

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/

function _putpages()
{
	$nb = $this->page;

	foreach ($this->pages as $k => $v) {
		if (!$k || strpos($v, $this->AliasNbPages) === false) {
			continue;
		}
		$this->pages[$k] = str_replace($this->AliasNbPages, $nb, $v);
	}

	if ($this->DefOrientation == 'P') {
		$wPt = $this->fwPt;
		$hPt = $this->fhPt;
	} else {
		$wPt = $this->fhPt;
		$hPt = $this->fwPt;
	}

	$filter = $this->compress ? '/Filter /FlateDecode ' : '';
	for($n=1;$n<=$nb;$n++)
	{
		//Page
		$this->_newobj();
		$this->_out('<</Type /Page');
		$this->_out('/Parent 1 0 R');
		$this->_out('/Resources 2 0 R');
		if(isset($this->PageLinks[$n]))
		{
			//Links
			$annots='/Annots [';
			foreach($this->PageLinks[$n] as $pl)
			{
				$rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
				$annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
				if(is_string($pl[4]))
					$annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
				else
				{
					$l=$this->links[$pl[4]];
					$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$hPt-$l[1]*$this->k);
				}
			}
			$this->_out($annots.']');
		}
		$this->_out('/Contents '.($this->n+1).' 0 R>>');
		$this->_out('endobj');
		//Page content
		$p = $this->compress ? gzcompress($this->pages[$n], 9) : $this->pages[$n];
		$this->_newobj();
		$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
		$this->_out('stream');
		$this->_out($p);
		$this->_out('endstream');
		$this->_out('endobj');
	}
	//Pages root
	$this->offsets[1]=strlen($this->buffer);
	$this->_out('1 0 obj');
	$this->_out('<</Type /Pages');
	$kids='/Kids [';
	for($i=0;$i<$nb;$i++)
		$kids.=(3+2*$i).' 0 R ';
	$this->_out($kids.']');
	$this->_out('/Count '.$nb);
	$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
	$this->_out('>>');
	$this->_out('endobj');
}

function _enddoc()
{
	$this->_putpages();
	foreach ($this->fonts as $k => $font) {
		//Font objects
		$this->fonts[$k]['n'] = $this->n + 1;

		//Standard font
		$this->_newobj();
		$this->_out('<</Type /Font');
		$this->_out('/BaseFont /'.$font['name']);
		$this->_out('/Subtype /Type1');
		$this->_out('/Encoding /WinAnsiEncoding');
		$this->_out('>>');
		$this->_out('endobj');
	}

	//Resource dictionary
	$this->offsets[2] = strlen($this->buffer);
	$this->_out('2 0 obj');
	$this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
	$this->_out('/Font <<');
	foreach ($this->fonts as $font) {
		$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
	}
	$this->_out('>>');
	$this->_out('>>');
	$this->_out('endobj');
	$this->_putbookmarks();
	//Info
	$this->_newobj();
	$this->_out('<<');
	$this->_out('/Producer '.$this->_textstring('FUDforum '.$GLOBALS['FORUM_VERSION']));
	if ($this->title) {
		$this->_out('/Title '.$this->_textstring($this->title));
	}
	if ($this->subject) {
		$this->_out('/Subject '.$this->_textstring($this->subject));
	}
	if ($this->author) {
		$this->_out('/Author '.$this->_textstring($this->author));
	}
	if ($this->creator) {
		$this->_out('/Creator '.$this->_textstring($this->creator));
	}
	$this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
	$this->_out('>>');
	$this->_out('endobj');
	//Catalog
	$this->_newobj();
	$this->_out('<<');
	$this->_out('/Type /Catalog');
	$this->_out('/Pages 1 0 R');
	$this->_out('/OpenAction [3 0 R /FitH null]');
	$this->_out('/PageLayout /OneColumn');

	if (!empty($this->outlines)) {
		$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
		$this->_out('/PageMode /UseOutlines');
	}
	$this->_out('>>');
	$this->_out('endobj');
	//Cross-ref
	$o=strlen($this->buffer);
	$this->_out('xref');
	$this->_out('0 '.($this->n+1));
	$this->_out('0000000000 65535 f ');
	for($i=1;$i<=$this->n;$i++)
		$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
	//Trailer
	$this->_out('trailer');
	$this->_out('<<');
	$this->_out('/Size '.($this->n+1));
	$this->_out('/Root '.$this->n.' 0 R');
	$this->_out('/Info '.($this->n-1).' 0 R');
	$this->_out('>>');
	$this->_out('startxref');
	$this->_out($o);
	$this->_out('%%EOF');
	$this->state=3;
}

function _newobj()
{
	//Begin a new object
	$this->offsets[++$this->n] = strlen($this->buffer);
	$this->_out($this->n.' 0 obj');
}

function _textstring($s)
{
	return '('.str_replace($this->_esp_s, $this->_esp_d, $s).')';
}

function _escape($s)
{
	return str_replace($this->_esp_s, $this->_esp_d, $s);
}

function _out($s)
{
	//Add a line to the document
	if ($this->state == 2) {
		$this->pages[$this->page] .= $s."\n";
	} else {
		$this->buffer .= $s . "\n";
	}
}

function Bookmark($txt, $level=0)
{
	$this->outlines[] = array('t' => $txt, 'l' => $level, 'y' => $this->y, 'p' => $this->page);
}

function _putbookmarks()
{
	if (empty($this->outlines)) {
		return;
	}

	$nb = count($this->outlines);

	$lru = array();
	$level = 0;
	foreach ($this->outlines as $i => $o) {
		if($o['l'] > 0) {
			$parent = $lru[$o['l']-1];
			//Set parent and last pointers
			$this->outlines[$i]['parent'] = $parent;
			$this->outlines[$parent]['last'] = $i;
			if ($o['l'] > $level) {
				//Level increasing: set first pointer
				$this->outlines[$parent]['first'] = $i;
			}
		} else {
			$this->outlines[$i]['parent'] = $nb;
		}

		if($o['l'] <= $level && $i > 0) {
			//Set prev and next pointers
			$prev = $lru[$o['l']];
			$this->outlines[$prev]['next'] = $i;
			$this->outlines[$i]['prev'] = $prev;
		}
		$lru[$o['l']] = $i;
		$level = $o['l'];
	}

	//Outline items
	$n = $this->n + 1;
	foreach($this->outlines as $i => $o) {
		$this->_newobj();
		$this->_out('<</Title '.$this->_textstring($o['t']));
		$this->_out('/Parent '.($n+$o['parent']).' 0 R');
		if(isset($o['prev']))
			$this->_out('/Prev '.($n+$o['prev']).' 0 R');
		if(isset($o['next']))
			$this->_out('/Next '.($n+$o['next']).' 0 R');
		if(isset($o['first']))
			$this->_out('/First '.($n+$o['first']).' 0 R');
		if(isset($o['last']))
			$this->_out('/Last '.($n+$o['last']).' 0 R');
		$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]',1+2*$o['p'],($this->h-$o['y'])*$this->k));
		$this->_out('/Count 0>>');
		$this->_out('endobj');
	}

	//Outline root
	$this->_newobj();
	$this->OutlineRoot = $this->n;
	$this->_out('<</Type /Outlines /First '.$n.' 0 R');
	$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
	$this->_out('endobj');
}

//End of class
}
?>
