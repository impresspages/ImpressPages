function LibTabs(my_name, hidden_class, visible_class, allow_hide){
  this.addTab = addTab;
  this.switchTab = switchTab;
  this.hideAll = hideAll;
  this.switchFirst = switchFirst;	
  
  var elem;
  var links
  var hidden;
  var visible;
  var my_name;
  
  this.elem = new Array();
  this.links = new Array();
  this.hidden = hidden_class;
  this.visible = visible_class;
	this.allow_hide = allow_hide;
  this.my_name = my_name;
  

  function addTab(link_id, element_id){
    this.elem.push(element_id);
    this.links.push(link_id);
    eval('document.getElementById(\'' + link_id + '\').onclick = function(){' + this.my_name + '.switchTab(\'' + element_id + '\');};');
  }

  function switchTab(element_id){
     var i=0;
     while(i<this.elem.length){
				if(this.elem[i] == element_id && 
					(!this.allow_hide || document.getElementById(this.links[i]).className != this.visible)
					){
          document.getElementById(this.elem[i]).style.display = 'inline';
          document.getElementById(this.links[i]).className =  this.visible;
        }else{ 
          document.getElementById(this.elem[i]).style.display = 'none';
          document.getElementById(this.links[i]).className =  this.hidden;
        }
      i++;
            
     }
  }

  function switchFirst(){
    if(this.elem[0])
      this.switchTab(this.elem[0]);
  }

  
  function hideAll(){
     for(var nr in this.elem){
        document.getElementById(this.elem[nr]).style.display = 'none';
     }
  }

}