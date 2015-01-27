L.Contao=L.Class.extend({includes:L.Mixin.Events,statics:{ATTRIBUTION:' | <a href="http://contao-leaflet.netzmacht.de/" title="Leaflet extension for Contao CMS">netzmacht <em>creative</em></a>'},maps:{},icons:{},initialize:function(){L.Icon.Default.imagePath="assets/leaflet/libs/leaflet/images",this.setGeoJsonListeners(L.GeoJSON)},addMap:function(t,e){return this.maps[t]=e,this.fire("map:added",{id:t,map:e}),this},getMap:function(t){return"undefined"==typeof this.maps[t]?null:this.maps[t]},addIcon:function(t,e){return this.icons[t]=e,this.fire("icon:added",{id:t,icon:e}),this},loadIcons:function(t){for(var e=0;e<t.length;e++){var o=L[t[e].type](t[e].options);this.addIcon(t[e].id,o)}},getIcon:function(t){return"undefined"==typeof this.icons[t]?null:this.icons[t]},load:function(t,e,o,n,i){var s=this.createRequestUrl(t,i),r=omnivore[e](s,o,n);return i&&(L.stamp(r),i.options.dynamicLoad&&"fit"==r.options.boundsMode&&(r.options.requestHash=t,i.on("moveend",r.refreshData,r),i.on("layerremove",function(t){t.layer===r&&i.off("moveend",r.updateBounds,r)})),i.fire("dataloading",{layer:r}),r.on("ready",function(){i.calculateFeatureBounds(r),i.fire("dataload",{layer:r})}),r.on("error",function(){i.fire("dataload",{layer:r})})),r},pointToLayer:function(t,e){var o="marker",n=null;if(t.properties&&(t.properties.bounds=!0,t.properties.type&&(o=t.properties.type),t.properties.arguments&&(n=L[o].apply(L[o],t.properties.arguments),L.Util.setOptions(n,t.properties.options))),null===n&&(n=L[o](e,t.properties.options)),t.properties){if(t.properties.radius&&n.setRadius(t.properties.radius),t.properties.icon){var i=this.getIcon(t.properties.icon);i&&n.setIcon(i)}this.bindPopupFromFeature(n,t)}return this.fire("point:added",{marker:n,feature:t,latlng:e,type:o}),n},onEachFeature:function(t,e){t.properties&&(L.Util.setOptions(e,t.properties.options),this.bindPopupFromFeature(e,t),this.fire("feature:added",{feature:t,layer:e}))},bindPopupFromFeature:function(t,e){e.properties&&(e.properties.popup?t.bindPopup(e.properties.popup,e.properties.popupOptions):e.properties.popupContent&&t.bindPopup(e.properties.popupContent,e.properties.popupOptions))},setGeoJsonListeners:function(t){t&&t.prototype&&(t.prototype.options={pointToLayer:this.pointToLayer.bind(this),onEachFeature:this.onEachFeature.bind(this)})},createRequestUrl:function(t,e){var o,n="leaflet",i=document.location.search.substr(1).split("&");if(t=encodeURIComponent(t),""==i)t=document.location.pathname+"?"+[n,t].join("=");else{for(var s,r=i.length;r--;)if(s=i[r].split("="),s[0]==n){s[1]=t,i[r]=s.join("=");break}0>r&&(i[i.length]=[n,t].join("=")),t=document.location.pathname+i.join("&")}return e&&e.options.dynamicLoad&&(o=e.getBounds(),t+="&f=bbox&v=",t+=o.getSouth()+","+o.getWest(),t+=","+o.getNorth()+","+o.getEast()),t}}),L.contao=new L.Contao,L.Control.Attribution.addInitHook(function(){this.options.prefix+=L.Contao.ATTRIBUTION}),L.Control.Attribution.include({setPrefix:function(t){return-1===t.indexOf(L.Contao.ATTRIBUTION)&&(t+=L.Contao.ATTRIBUTION),this.options.prefix=t,this._update(),this}}),L.GeoJSON.include({refreshData:function(t){var e=L.geoJson(),o=this;e.on("ready",function(){var t,e=o.getLayers();for(t=0;t<e.length;t++)o.removeLayer(e[t]);for(e=this.getLayers(),t=0;t<e.length;t++)this.removeLayer(e[t]),o.addLayer(e[t])}),omnivore.geojson(L.contao.createRequestUrl(this.options.requestHash,t.target),null,e)}}),L.Map.include({_dynamicBounds:null,calculateFeatureBounds:function(t,e){if(t){if(!this.options.adjustBounds&&!e)return;this._scanForBounds(t)}else this.eachLayer(this._scanForBounds,this);console.log(this._dynamicBounds),this._dynamicBounds&&this.fitBounds(this._dynamicBounds)},_scanForBounds:function(t){var e;!t.feature||t.feature.properties&&t.feature.properties.ignoreForBounds?L.MarkerClusterGroup&&t instanceof L.MarkerClusterGroup&&t.options.boundsMode&&"extend"==t.options.boundsMode?(e=t.getBounds(),e.isValid()&&(this._dynamicBounds?this._dynamicBounds.extend(e):this._dynamicBounds=L.latLngBounds(e.getSouthWest(),e.getNorthEast()))):(!t.options||t.options&&t.options.boundsMode&&!t.options.ignoreForBounds)&&t.eachLayer&&t.eachLayer(this._scanForBounds,this):t.getBounds?(e=t.getBounds(),e.isValid()&&(this._dynamicBounds?this._dynamicBounds.extend(e):this._dynamicBounds=L.latLngBounds(e.getSouthWest(),e.getNorthEast()))):t.getLatLng&&(e=t.getLatLng(),this._dynamicBounds?this._dynamicBounds.extend(e):this._dynamicBounds=L.latLngBounds(e,e))}});