;(function ( $, window, document, undefined ) {

    var pluginName = "entitySelector";

    // The actual plugin constructor
    function EntitySelector( element, options ) {
        this.element = element;
        this.$el = $(element);
        this.options = $.extend( {}, $.EntitySelector.defaults, options) ;
        this._name = pluginName;
        this.init();
    }

    EntitySelector.prototype = {

        init: function() {
        	var self = this;
       	
        	var s2 = $(".es-field", this.$el).select2({placeholder:'-', width:250, allowClear:true,
        		minimumInputLength:0, 
        		query:function(q){
        			if (!q.term || !q.term.length) {
    					self.loadItems(null, function(data){
    						q.callback({results: data, more:false});
    					});
        			} else {
        				if (self.items) {
        					var queryFn = Select2.query.local(self.items);
        					queryFn(q);
        				} else {
        					self.loadItems(q.term, function(data){
        							q.callback({results: data, more:false});
        					});
        				}
        			}
        		}
        	})
        	.on('change', function(){
        		var v = $(this).select2('val');
        		$('.es-value', self.$el).val(v);
        		var item = self.getItemById(v);
        		if (item && item.link) {
        			$('.es-link', self.$el).attr('href', item.link);
        		}
        	});  
        	
        	if (this.options.value && this.options.entity) {
        		try {
	        		s2.select2('data', this.options.entity);
	        		s2.select2('val',this.options.value);
        		} catch(e) {}
        	}
        },

        loadItems : function(query, s2callback) {
        	var self = this;
        	if (this.items) {
        		s2callback(this.items)
        	} else {
            	$.ajax({
            		url:this.options.ajaxUrl || "",
            		data:{ajaxId:this.options.ajaxId, ajaxView:this.options.ajaxView},
            		type:'post',
            		dataType:'json',
            		success: function(data) {
            			self.items = data;
            			s2callback(data);
            		}
            	});
        	}
        },
        
        getItemById : function(id) {
        	var found = false;
        	$.each(this.items, function(i, item){
        		if (item.id == id) {
        			found = item;
        		}
        	});
        	return found;
        } 
        
    };
    
    $.EntitySelector = EntitySelector;
    
    $.EntitySelector.defaults = {
    };

    $.fn[pluginName] = function ( options ) {
    	var callArgs = Array.prototype.slice.call(arguments);
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new EntitySelector( this, options ));
            }
        	if (typeof options == 'string') {
        		var te = $.data(this, "plugin_" + pluginName);
        		te[options].apply(te, callArgs.slice(1));
        	}
        });
    };

})( jQuery, window, document );
