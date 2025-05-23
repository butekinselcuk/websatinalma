/*
 * selectToDo - jQuery plugin for select checkbox
 *
 * Copyright (c) 2014 Elric Huang
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/elrichuang/jquery.selectToDo.js
 *
 * Version:  0.1.3
 *
 */
;(function ($){
	$.fn.selectToDo = function (options){
		var settings = $.extend({},{
			"selectAllButton"    : $("#selectAll"),
			"selectNoneButton"   : $("#selectNone"),
			"selectInvertButton" : $("#selectInv"),
		}, options);
		
		var element = this;
		
		$(settings.selectAllButton).bind("click",function(){
			element.selectAll();
		});
		$(settings.selectNoneButton).bind("click",function(){
			element.selectNone();
		});
		$(settings.selectInvertButton).bind("click",function(){
			element.selectInvert();
		});
		
		this.selectAll = function(){//全选
			element.prop('checked', true);
		};
		
		this.selectNone = function(){//全不选
			element.prop('checked', false);
		};
		
		this.selectInvert = function(){//反选
			element.each(function(){
				if(this.checked){
					$(this).prop('checked', false);
				}else{
					$(this).prop('checked', true);
				}
			});
		};
		
		this.result = function(){
			var checkVal=[];
			element.each(function(){
				if(this.checked){
					checkVal.push($(this).val());
				}
			});
			if(checkVal.length > 0)
			{
				// 引用回调函数
			    return checkVal.join(",");
			}else{
				return null;
			}
		};
		
		return this;
	};
})(jQuery);