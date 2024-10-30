/*
 * mango_popup 1.0.0
 * 
 * 
 *
 * Copyright 2017, Mango Extensions
*/

!function(modules){function __webpack_require__(moduleId){if(installedModules[moduleId])return installedModules[moduleId].exports;var module=installedModules[moduleId]={i:moduleId,l:!1,exports:{}};return modules[moduleId].call(module.exports,module,module.exports,__webpack_require__),module.l=!0,module.exports}var installedModules={};__webpack_require__.m=modules,__webpack_require__.c=installedModules,__webpack_require__.d=function(exports,name,getter){__webpack_require__.o(exports,name)||Object.defineProperty(exports,name,{configurable:!1,enumerable:!0,get:getter})},__webpack_require__.n=function(module){var getter=module&&module.__esModule?function(){return module.default}:function(){return module};return __webpack_require__.d(getter,"a",getter),getter},__webpack_require__.o=function(object,property){return Object.prototype.hasOwnProperty.call(object,property)},__webpack_require__.p="",__webpack_require__(__webpack_require__.s=0)}([function(module,exports,__webpack_require__){module.exports=__webpack_require__(1)},function(module,exports,__webpack_require__){"use strict";function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj}}var _jQuery=__webpack_require__(2),_jQuery2=_interopRequireDefault(_jQuery),_Popup=__webpack_require__(3),_Popup2=_interopRequireDefault(_Popup);(0,_jQuery2.default)(function($){$(document).ready(function(){$(".mango-popup-all-popup-wrapper .mango-popup").each(function(iterate,ele){!function(ele){new _Popup2.default(ele)}(ele)}),$(".mango-popup.mango-popup--shortcode").each(function(iterate,ele){!function(ele){new _Popup2.default(ele)}(ele)})})})},function(module,exports){module.exports=jQuery},function(module,exports,__webpack_require__){"use strict";function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj}}function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),_ExitIndent=__webpack_require__(4),_ExitIndent2=_interopRequireDefault(_ExitIndent),_InactiveTimer=__webpack_require__(5),_InactiveTimer2=_interopRequireDefault(_InactiveTimer),_Cookie=__webpack_require__(6),_Cookie2=_interopRequireDefault(_Cookie),_WooCommerce=__webpack_require__(7),_WooCommerce2=_interopRequireDefault(_WooCommerce),PopupDataCookie=_Cookie2.default.getCookie("mango-popup-data")?JSON.parse(_Cookie2.default.getCookie("mango-popup-data")):{},PopupDataSession=_Cookie2.default.getCookie("mango-popup-data-session")?JSON.parse(_Cookie2.default.getCookie("mango-popup-data-session")):{};Date.prototype.mangoGetWeek=function(){var jan4th=new Date(this.getFullYear(),0,4);return Math.ceil(((this-jan4th)/864e5+jan4th.getDay()+1)/7)};var Popup=function(){function Popup(ele){_classCallCheck(this,Popup),this.$this=jQuery(ele),this.id=this.$this.attr("id"),this.dataID=this.$this.data("id"),this.animation=this.$this.data("animation"),this.whenPopupDisplay=this.$this.data("when-popup-display"),this.backgroundColor=this.$this.data("background-color"),this.$content=this.$this.find(".mango-popup__content"),this.$container=this.$this.find(".mango-popup-container"),this.$close=this.$this.find(".mango-popup__close"),this.howOftenPopupDisplay=this.$this.data("how-often"),this.$buttonSubmit=this.$this.find(".mango-popup-button-cta"),this.$form=this.$this.find("form"),this.backgroundColorEffected=this.$this.find(".mango-popup-background-color-effected"),this.template=this.$this.data("template"),this.handleHowOftenPopupDisplay()&&(this.handleProcessHtml(),this.$this.data("woocommerce-hotdeal")&&this.handleWooCommerce(),this.handleWhenPopupDisplay(),this.handleClose(),this.handleFormSubmit())}return _createClass(Popup,[{key:"handleWooCommerce",value:function(){this.woocommerce={},this.woocommerce.hotdeal=this.$this.data("woocommerce-hotdeal"),this.woocommerce.label=this.$this.data("woocommerce-label"),this.woocommerce.maxProduct=this.$this.data("woocommerce-max-product"),this.woocommerce.productTemplate=mango_popup_product_template[this.template],this.renderWooCommerceData()}},{key:"renderWooCommerceData",value:function(){switch(this.woocommerce.hotdeal){case"day":this.woocommerce.data=latest_deals_data||{};break;case"best":this.woocommerce.data=best_price_deals_data||{};break;default:this.woocommerce.data={}}this.wooCommerceInstance=new _WooCommerce2.default,this.wooCommerceInstance.setContainer(this.$this),this.wooCommerceInstance.setData(this.woocommerce.data),this.wooCommerceInstance.setMaxProduct(this.woocommerce.maxProduct),this.wooCommerceInstance.setProductTemplate(this.woocommerce.productTemplate),this.wooCommerceInstance.setProductDetailLabel(this.woocommerce.label),this.wooCommerceInstance.fill()}},{key:"handleHowOftenPopupDisplay",value:function(){return!this.howOftenPopupDisplay||"always"===this.howOftenPopupDisplay||(this.howOftenPopupDisplay&&"once-a-session"===this.howOftenPopupDisplay?this.checkHowOftenSession():this.checkHowOftenCookie())}},{key:"checkHowOftenSession",value:function(){return!PopupDataSession||!PopupDataSession[this.dataID]}},{key:"checkHowOftenCookie",value:function(){if(!PopupDataCookie[this.dataID])return!0;var currentDate=new Date,viewedDate=new Date(1e3*PopupDataCookie[this.dataID]),currentYear=currentDate.getFullYear(),viewedYear=viewedDate.getFullYear(),currentWeek=currentDate.mangoGetWeek(),viewedWeek=viewedDate.mangoGetWeek(),currentDay=currentDate.getDate(),viewedDay=viewedDate.getDate();switch(this.howOftenPopupDisplay){case"once-a-day":if(currentYear>viewedYear)return!0;if(currentDay>viewedDay)return!0;break;case"once-a-week":if(currentYear>viewedYear)return!0;if(currentWeek>viewedWeek)return!0}return!1}},{key:"handleProcessHtml",value:function(){this.backgroundColor&&this.setBackgroundColor(this.backgroundColor)}},{key:"setBackgroundColor",value:function(color){this.backgroundColorEffected&&this.backgroundColorEffected.length?this.setBackgroundColorEffected(color):this.setBackgroundColorContainer(color)}},{key:"setBackgroundColorContainer",value:function(color){this.$container.css("background-color",color)}},{key:"setBackgroundColorEffected",value:function(color){this.backgroundColorEffected.each(function(){jQuery(this).css("background-color",color)})}},{key:"handleWhenPopupDisplay",value:function(){switch(this.whenPopupDisplay){case"page-loaded":this.displayPageLoaded();break;case"before-user-exit":this.displayBeforeUserExit();break;case"scroll-bottom":this.displayScrollBottom();break;case"scroll-center":this.displayScrollCenter();break;case"in-active-one-minute":this.displayInactive(6e4);break;default:this.displayPageLoaded()}}},{key:"displayBeforeUserExit",value:function(){var self=this;new _ExitIndent2.default(this.$this,function(){self.open()})}},{key:"displayPageLoaded",value:function(){var self=this;jQuery(document).ready(function(){self.open()})}},{key:"displayScrollBottom",value:function(){var self=this;jQuery(window).scroll(function(){jQuery(window).scrollTop()+jQuery(window).height()>=jQuery(document).height()&&self.open()})}},{key:"displayScrollCenter",value:function(){var self=this;jQuery(window).scroll(function(){jQuery(window).scrollTop()>=(jQuery(document).height()-jQuery(window).height())/2&&self.open()})}},{key:"displayInactive",value:function(miliseconds){var self=this;new _InactiveTimer2.default(function(){self.open()},miliseconds)}},{key:"startAnimation",value:function(){this.$content.addClass("animated").addClass(this.animation);var self=this;this.$content.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",function(){self.endAnimation()})}},{key:"endAnimation",value:function(){this.$content.removeClass("animated").removeClass(this.animation)}},{key:"open",value:function(){this.$this.hasClass("active")||this.$this.data("opened")||(this.$this.addClass("active"),this.$this.data("opened",!0),this.startAnimation(),this.$this.data("preview")||this.handleAfterOpenPopup())}},{key:"close",value:function(){this.$this.remove()}},{key:"handleClose",value:function(){var self=this;this.$close.each(function(){jQuery(this).on("click",function(){self.close()})})}},{key:"handleAfterOpenPopup",value:function(){this.howOftenPopupDisplay&&"once-a-session"===this.howOftenPopupDisplay?this.setSessionViewed():this.howOftenPopupDisplay&&"always"!==this.howOftenPopupDisplay&&this.setCookieViewed(),this.ajaxAfterOpenPopup()}},{key:"setCookieViewed",value:function(){var date=new Date,currentTime=new Date,currentTimeStamp=Math.floor(currentTime/1e3);date.setTime(date.getTime()+2592e6);var expires=date.toGMTString();PopupDataCookie.time_zone=Intl.DateTimeFormat().resolvedOptions().timeZone||"UTC",PopupDataCookie[this.dataID]=currentTimeStamp,_Cookie2.default.setCookie("mango-popup-data",JSON.stringify(PopupDataCookie),expires,"","/")}},{key:"setSessionViewed",value:function(){PopupDataSession[this.dataID]=!0,_Cookie2.default.setCookie("mango-popup-data-session",JSON.stringify(PopupDataSession),"","","/")}},{key:"ajaxAfterOpenPopup",value:function(){var self=this;jQuery.ajax({url:MangoPopupFrontendL10N.ajaxUrl,method:"POST",data:{nonce:self.$this.data("nonce-open"),action:"mango_popup_after_open",id:self.dataID}})}},{key:"handleFormSubmit",value:function(){var self=this;this.$form.on("submit",function(){var formData=jQuery(this).serialize(),oldValue=self.$buttonSubmit.html(),buttonClicked=jQuery(this).find('button[type="submit"]:focus');return formData&&self.$buttonSubmit.html('<i class="fa fa-fw fa-spin fa-spinner mango-popup__button-icon-loading"></i>'),jQuery(this).ajaxSubmit({method:"POST",url:MangoPopupFrontendL10N.ajaxUrl,data:{nonce:self.$this.data("nonce-submit"),id:self.dataID,action:"mango_popup_form_submit"}}),setTimeout(function(){var targetLink="";self.fromShortCode()?formData&&(self.$buttonSubmit.html('<i class="fa fa-fw fa-check mango-popup__button-icon-loading"></i>'),self.$form.trigger("reset"),setTimeout(function(){self.$buttonSubmit.html(oldValue)},500)):buttonClicked.data("href")||self.close(),(targetLink=buttonClicked.data("href")?buttonClicked.data("href"):self.$this.data("target-link")||"")&&(window.location.href=encodeURI(targetLink))},500),!1})}},{key:"fromShortCode",value:function(){return this.$this.hasClass("mango-popup--shortcode")}}]),Popup}();exports.default=Popup},function(module,exports,__webpack_require__){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),ExitIndent=function(){function ExitIndent($popup,callback){_classCallCheck(this,ExitIndent),this.id=$popup.attr("id"),this.callback=callback,this.sensitivity=20,this.$document=jQuery(document),this.delay=0,this.startEvent()}return _createClass(ExitIndent,[{key:"startEvent",value:function(){this.$document.bind("mouseleave",this.handleMouseLeave.bind(this)),this.$document.bind("mouseenter",this.handleMouseEnter.bind(this))}},{key:"handleMouseLeave",value:function(e){e.clientY>this.sensitivity||(this.delayTimer=setTimeout(this.fireEvent.bind(this),this.delay))}},{key:"handleMouseEnter",value:function(){this.delayTimer&&(clearTimeout(this.delayTimer),this.delayTimer=null)}},{key:"fireEvent",value:function(){this.callback(),this.setViewed()}},{key:"setViewed",value:function(){this.$document.unbind("mouseleave",this.handleMouseLeave.bind(this)),this.$document.unbind("mouseenter",this.handleMouseEnter.bind(this))}}]),ExitIndent}();exports.default=ExitIndent},function(module,exports,__webpack_require__){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),timer=0,self=void 0,InactiveTimer=function(){function InactiveTimer(callback,miliseconds){_classCallCheck(this,InactiveTimer),this.$body=jQuery("body"),this.miliseconds=miliseconds,this.$body.on("click mousemove keyup",function(){self.resetTimer()}),this.callback=callback,self=this}return _createClass(InactiveTimer,[{key:"fireEvent",value:function(){self.callback()}},{key:"resetTimer",value:function(){clearTimeout(timer),timer=setTimeout(this.fireEvent,this.miliseconds)}}]),InactiveTimer}();exports.default=InactiveTimer},function(module,exports,__webpack_require__){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),_parsedCookie=void 0,Cookie=function(){function Cookie(){_classCallCheck(this,Cookie)}return _createClass(Cookie,null,[{key:"getCookie",value:function(name){return(_parsedCookie||Cookie.parseCookie())[name]||!1}},{key:"parseCookie",value:function(){for(var cookies=document.cookie.split("; "),cookieReturn={},i=0;i<=cookies.length-1;i++){var cookie=cookies[i].split("=");cookieReturn[cookie[0]]=cookie[1]}return _parsedCookie=cookieReturn,cookieReturn}},{key:"setCookie",value:function(name,value,expire,domain,path){var cookie=name+"="+value+";";expire&&(cookie+="expires="+expire+";"),domain&&(cookie+="domain="+domain+";"),path&&(cookie+="path="+path+";"),document.cookie=cookie}}]),Cookie}();exports.default=Cookie},function(module,exports,__webpack_require__){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),_PopupImage=__webpack_require__(8),_PopupImage2=function(obj){return obj&&obj.__esModule?obj:{default:obj}}(_PopupImage),WooCommerce=function(){function WooCommerce(){_classCallCheck(this,WooCommerce)}return _createClass(WooCommerce,[{key:"setContainer",value:function(container){this.$container=container,this.$container.hasClass("mango-popup-product-container")?this.$productContainer=this.$container:this.$productContainer=this.$container.find(".mango-popup-product-container")}},{key:"setData",value:function(data){this.data=data}},{key:"setProductTemplate",value:function(html){this.productTemplate=html}},{key:"setMaxProduct",value:function(number){this.maxProduct=number}},{key:"fill",value:function(){var data=this.data,popupImage=void 0,index=void 0;for(this.$productContainer.html(""),data.length||this.$productContainer.html('<p style="text-align: center;">There is no product deal available. This popup won\'t display.</p>'),index=0;index<this.maxProduct;index++)if(data[index]){this.$productContainer.append(this.productTemplate);var $product=this.$productContainer.find(".mango-popup-product").last();$product.find(".mango-popup-product-title").html(data[index].title),$product.find(".mango-popup-product-regular-price").html(data[index].regularPriceFormatted),$product.find(".mango-popup-product-sale-price").html(data[index].salePriceFormatted),popupImage=new _PopupImage2.default($product.find(".mango-popup-product-image").get(0)),popupImage.change(data[index].image),$product.find(".mango-popup-product-url").attr("data-href",data[index].permalink),this.fillProductDetailLabel()}}},{key:"fillProductDetailLabel",value:function(){var self=this;this.$container.find(".mango-popup-product .mango-popup-product-detail-label").each(function(){jQuery(this).html(self.productDetailLabel)})}},{key:"setProductDetailLabel",value:function(value){this.productDetailLabel=value}}]),WooCommerce}();exports.default=WooCommerce},function(module,exports,__webpack_require__){"use strict";function _classCallCheck(instance,Constructor){if(!(instance instanceof Constructor))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(exports,"__esModule",{value:!0});var _createClass=function(){function defineProperties(target,props){for(var i=0;i<props.length;i++){var descriptor=props[i];descriptor.enumerable=descriptor.enumerable||!1,descriptor.configurable=!0,"value"in descriptor&&(descriptor.writable=!0),Object.defineProperty(target,descriptor.key,descriptor)}}return function(Constructor,protoProps,staticProps){return protoProps&&defineProperties(Constructor.prototype,protoProps),staticProps&&defineProperties(Constructor,staticProps),Constructor}}(),PopupImage=function(){function PopupImage(ele){_classCallCheck(this,PopupImage),this.$this=jQuery(ele)}return _createClass(PopupImage,[{key:"change",value:function(url){this.$this.is("img")?this.changeImageSrc(url):this.changeBackgroundImage(url)}},{key:"changeImageSrc",value:function(url){this.$this.attr("src",url)}},{key:"changeBackgroundImage",value:function(url){var backgroundImageUrl="url("+url+")";this.$this.css("background-image",backgroundImageUrl)}},{key:"getType",value:function(){return this.$this.is("img")?"image":"background"}}]),PopupImage}();exports.default=PopupImage}]);