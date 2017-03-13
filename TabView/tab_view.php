<?php
/******************************
	
	Filename: tab_view.php
	Created: December 12, 2002 
	Author: Brad Touesnard
	Copyright: Copyright © 2002 Zenutech.com

	Last Modified: 
	Last Modified By: 

 ******************************/

class TabView {
	
	var $BackColor;
	var $BodyBackground;
	var $Class;
	var $ImagePath;
	var $Orientation;
	var $QueryString;
	var $SelectedBackColor;
	var $SelectedBold;
	var $SelectedForeColor;
	var $StartTab;
	var $Image;
	var $SelectedImage;
	var $DHTML;
	var $ForceDHTML;
	 		
	var $TB;

	var $i;
	var $arrTabs;

	
	/**
		Class constructor.
	*/
	function TabView() {
		$this->BackColor = "#EEEEFF";
		$this->BodyBackground = "#FFFFFF";
		$this->Class = "";
		$this->ImagePath = "";
		$this->Orientation = 0;
		$this->QueryString = "";
		$this->SelectedBackColor = "#CCCCFF";
		$this->SelectedBold = false;
		$this->SelectedForeColor = "";
		$this->StartTab = "";
		$this->TabWidth = 0;
		$this->DHTML = "";
		$this->ForceDHTML = false;

		$this->TB = $_GET['TB'];

		$this->i = 0;
	}
	
	/**
		Print the tabs to the screen.
	*/
	function Show() {
		$arrTabs = $this->arrTabs;

		if (count($arrTabs) == 0) {
			return 1;
		}

		if (strlen($this->TB) > 0) {
			$strSelectedKey = $this->TB;
		}
		else {
			$strSelectedKey = $this->StartTab;
		}

		if (strlen($this->Class) > 0) {
			$strStyleClass = "class=\"$this->Class\"";
		}
		else {
			$strStyleClass = "";
		}

		echo "
			<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
			<tr>";

		if ($this->Orientation == 1) {
			echo "
				<td width=\"100%\">&nbsp;</td>";
		}


		echo "
				<td align=\"left\">
					<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>";


		for ($i = 0; $i < count($arrTabs); $i++) {
			$objTab = $arrTabs[$i];
			
			if (strlen($this->DHTML) > 0) {
				if ($this->ForceDHTML) {
					$objTab->ForceDHTML = 1;
				}
				$objTab->DHTML = $this->DHTML;
			}
			
			$strDHTML = str_replace("#TAB#",$objTab->strKey,$objTab->DHTML);
			$strDHTML = str_replace("#PAGE#",$_SERVER['SCRIPT_NAME'],$strDHTML);
			$strDHTML = str_replace("#QUERYSTRING#",$_SERVER['QUERY_STRING'],$strDHTML);

			
			// Start URL attributes			 
			if ($objTab->ForceDHTML) {
				$strURL = "";
			}
			else {
				if (strlen($this->QueryString) > 0) {
					$strURL = "href=\"$objTab->strURL?TB={$objTab->strKey}&{$this->QueryString}\"";
				}
				else {
					$strURL = "href=\"$objTab->strURL?TB={$objTab->strKey}\"";
				}
			}

			if (strlen($objTab->strTarget) == 0) {
				$strTarget = "";
			}
			else {
				$strTarget = "target=\"{$objTab->strTarget}\"";
			}
			// End URL attributes


			
			// Start text formatting
			$strTextFormatStart = "<span style=\"";
			$strTextFormatEnd = "</span>";
			if ($objTab->Bold || ($this->SelectedBold && $objTab->strKey == $strSelectedKey)) {
				$strTextFormatStart .= "font-weight:bold;";
			}

			if (strlen($objTab->ForeColor) > 0) {
				$strTextFormatStart .= "color:{$objTab->ForeColor};";
			}
			elseif (strlen($this->SelectedForeColor) > 0 && $objTab->strKey == $strSelectedKey) {
				$strTextFormatStart .= "color:{$this->SelectedForeColor};";
			}
			$strTextFormatStart .= "\">";
			// End text formatting



			if (strlen($objTab->TabWidth) == 0) {
				$strTabWidth = "";
			}
			else {
				$strTabWidth = "width=\"{$objTab->TabWidth}\"";
			}


			if ($objTab->strKey == $strSelectedKey) {
				echo "
					<td width=\"16\" valign=\"top\">
						<img src=\"{$this->ImagePath}/tb_left_sel.gif\" width=\"14\" height=\"21\"></td>
					<td $strTabWidth $strStyleClass style=\"background-color:{$this->SelectedBackColor}; text-align:center; white-space:nowrap;\" nowrap>
						<label title=\"{$objTab->strCaption}\">";

				if (strlen($objTab->SelectedImage) > 0) {
					echo "<img src=\"{$objTab->SelectedImage}\" border=\"0\" align=\"absmiddle\">";
				}
				elseif (strlen($objTab->Image) > 0) {
					echo "<img src=\"{$objTab->Image}\" border=\"0\" align=\"absmiddle\">";
				}
				elseif (strlen($this->SelectedImage) > 0) {
					echo "<img src=\"{$this->SelectedImage}\" border=\"0\" align=\"absmiddle\">";
				}
				elseif (strlen($this->Image) > 0) {
					echo "<img src=\"{$this->Image}\" border=\"0\" align=\"absmiddle\">";
				}
					
				echo "
							$strTextFormatStart {$objTab->strText} $strTextFormatEnd</label></td>
					<td width=\"16\" valign=\"top\">
						<img src=\"{$this->ImagePath}/tb_right_sel.gif\" width=\"15\" height=\"21\"></td>";
			}
			else {
				echo "
					<td width=\"16\" valign=\"top\">
						<img src=\"{$this->ImagePath}/tb_left.gif\" width=\"14\" height=\"21\"></td>
					<td $strTabWidth $strStyleClass style=\"background-color:{$this->BackColor}; text-align:center; white-space:nowrap;\" nowrap>
						<a $strURL $strDHTML $strTarget title=\"{$objTab->strCaption}\">";

				if (strlen($objTab->Image) > 0) {
					echo "<img src=\"{$objTab->Image}\" border=\"0\" align=\"absmiddle\">";
				}
				elseif (strlen($this->Image) > 0) {
					echo "<img src=\"{$this->Image}\" border=\"0\" align=\"absmiddle\">";
				}

				echo "
							$strTextFormatStart {$objTab->strText} $strTextFormatEnd</a></td>
					<td width=\"16\" valign=\"top\">
						<img src=\"{$this->ImagePath}/tb_right.gif\" width=\"15\" height=\"21\"></td>";
			}
		}

		echo "
					</tr>
					</table>
				</td>";

		if ($this->Orientation == 0) {
			echo "
				<td width=\"100%\">&nbsp;</td>";
		}

		echo "
			</tr>
			</table>";
	}

	
	/**
		Adds a tab to the array.
		@param strKey unique identifier for this Tab object
		@param strText text to display on the tab
		@param strURL URL of target page
		@param strTarget the name of the frame or window in which the URL will be displayed
		@param strCaption text for the tool tip when hovering over the tab text
		@returns pointer to the new Tab object added
	*/
	function Add($strKey, $strText, $strURL, $strTarget, $strCaption) {
	    if ($this->duplicateKey($strKey)) {
	        echo "<b>Error:</b> Duplicate key found. The key \"$strKey\" has already been added.";
			exit;
	    }

		$objTab = new Tab($strKey, $strText, $strURL, $strTarget, $strCaption);

		$this->arrTabs[$this->i] = $objTab;
		$this->i++;

		return $objTab;
	}

	/**
	 * Checks if the tab key has already been defined.
	 * @param strKey the key string of the tab
	 * @returns true or false
	 */
	function duplicateKey($strKey) {
	    for ($i = 0; $i < count($this->arrTabs); $i++) {
	        if ($this->arrTabs[$i][0] == $strKey) {
	            return true;
	        }
	    }
		return false;
	} // end 
}


class Tab {

	var $strKey;
	var $strText;
	var $strURL;
	var $strTarget;
	var $strCaption;
	var $ForeColor;
	var $Bold;
	var $Image;
	var $SelectedImage;
	var $DHTML;
	var $ForceDHTML;
	var $TabWidth;


	/**
		Class constructor.
		@param strKey unique identifier for this Tab object
		@param strText text to display on the tab
		@param strURL URL of target page
		@param strTarget the name of the frame or window in which the URL will be displayed
		@param strCaption text for the tool tip when hovering over the tab text
	*/
	function Tab($strKey, $strText, $strURL, $strTarget, $strCaption) {
		$this->strKey = $strKey;
		$this->strText = $strText;
		$this->strURL = $strURL;
		$this->strTarget = $strTarget;
		$this->strCaption = $strCaption;
		$this->ForeColor = "";
		$this->Bold = false;
		$this->Image = "";
		$this->SelectedImage = "";
		$this->DHTML = "";
		$this->ForceDHTML = "";
		$this->TabWidth = "";
	}
}
?>