/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
function EditMenuManagementModuleDimensions(left_edge, left_offset){

	this.left_edge = left_edge;
	this.right_edge = left_edge + left_offset;
	this.dimensions = new Array();
	this.add_dimension = add_dimension;
	this.active_module_number = active_module_number;
	this.last_active='';
	function add_dimension(module_id, top, bottom){	
		this.dimensions[module_id] = [top, bottom];
	}

	function active_module_number(mouseX, mouseY){
	
		if(mouseX < this.left_edge || mouseX > this.right_edge || mouseY < 85){
			this.last_active = '';
			return '';
		}
		var i=0;
		var answer = 0;
		var min_distance = 10000;
		var on_bottom = true;
		while(this.dimensions[i]){
			//if(mouseY < (this.dimensions[i][0] + this.dimensions[i][1])/2)
			if(mouseY < this.dimensions[i][0] + this.dimensions[i][1]/2  )
				on_bottom = false;
			if(min_distance > Math.abs(mouseY - this.dimensions[i][0])){
					min_distance = Math.abs(mouseY - this.dimensions[i][0]);
					answer = i;
			}
			i++;
		}
		if(on_bottom)
			answer = 'end';
		this.last_active = answer;
	 return answer;
	}




}