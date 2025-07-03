jQuery(function($){
    stfsk.init();
    prestashop.on('updateProductList', function(data) {
        stfsk.init();
    });
    $(document).on('change', '.st_lower_input,.st_upper_input', function() {
        var val = $(this).val();
        var rang = $(this).closest('.st-range-box').find('.st-range');
        var jiazhong = rang.data('jiazhong');
        val = stfsk.deFormatPrice(val,jiazhong);
        if($(this).hasClass('st_lower_input'))
            rang[0].noUiSlider.set([val, null]);
        else
            rang[0].noUiSlider.set([null, val]);
    });
});


var stfsk = {
    'init': function(){
        $.each(this.jiazhong, function(k,v){
            if(!v.format.symbles.length)
                stfsk.learnFormat(k);
        });
        this.run();
    },
    'jiazhong':{'price':{'url':'','format': {'prefix':'', 'suffix':'','symbles':[], 'sample':'', 'decimals':2}},'weight':{'url':'','format': {'prefix':'', 'suffix':'','symbles':[], 'sample':'', 'decimals':6}}},
    'run' : function(){
        this.init_slide();
    },
    'init_slide': function(){
        $.each(this.jiazhong, function(k,v){
            var slides = $('.st-range[data-jiazhong="'+k+'"]');
            if(slides.length)
            {
                $.each(slides, function(index,slide_dom){
                    stfsk.chushihua(k, slide_dom);
                });
            }
        });
        var rangesliders = $('.st-range[data-jiazhong="rangeslider"]');
        if(rangesliders.length){
            $.each(rangesliders, function(index,slide_dom){
                stfsk.chushihua('rangeslider', slide_dom);
            });
        }
    },
    'chushihua': function(k, slide_dom){
        var has_error = false;
        var slide = $(slide_dom);
        var snapValues = [];
        var inputValues = [];
        if(stfacetdsearchkits.with_inputs==1 && k!='rangeslider')
            inputValues = [
                slide.closest('.st-range-box').find('.st_lower_input'),
                slide.closest('.st-range-box').find('.st_upper_input')
            ];
        else
            snapValues = [
                slide.closest('.st-range-box').find('.value-lower'),
                slide.closest('.st-range-box').find('.value-upper')
            ];

        var url = slide.data('url');
        var range_values = [];
        var params = {
            start: [ parseFloat(slide.data('lower')), parseFloat(slide.data('upper')) ],
            connect: true
        };
        var rangeslider_suffix = '';
        var rangeslider_prefix = '';
        if(stfacetdsearchkits.tooltips==1 && (k=='weight' || k=='price'))
            $.extend(params,{tooltips: [ stfsk.fomater[k], stfsk.fomater[k] ]});
        if(stfacetdsearchkits.vertical==1)
            $.extend(params,{orientation: 'vertical'});
        if(k=='weight'){
            $.extend(params,{range: {'min': [ parseFloat(slide.data('min')) ],'max': [ parseFloat(slide.data('max')) ]}, format: { 'to': function( value ){return value !== undefined && value.toFixed(stfsk.jiazhong[k].format.decimals);}, 'from': Number }});
            if(stfacetdsearchkits.weight_step>0)
                $.extend(params,{step: stfacetdsearchkits.weight_step});
        }
        else if(k=='price'){
            if(stfacetdsearchkits.price_step>0)
                $.extend(params,{step: stfacetdsearchkits.price_step});
            $.extend(params,{range: {'min': [ parseFloat(slide.data('min')) ],'max': [ parseFloat(slide.data('max')) ]}, format: { 'to': function( value ){return value !== undefined && value.toFixed(stfsk.jiazhong[k].format.decimals);}, 'from': Number }});
        }else if(k=='rangeslider'){
            var range_values_string = slide.data('values')+'';
            rangeslider_suffix = slide.data('suffix');
            rangeslider_prefix = slide.data('prefix');
            if(range_values_string && range_values_string.indexOf(',')!=-1){
                range_values = slide.data('values').split(',');
                $.each(range_values, function(rk,rv){
                    range_values[rk] = parseFloat(rv, 10);
                    // range_values[rk] = Number(rv);
                });
                var range_json = {};
                var range_length = range_values.length;
                var range_min = range_values[0];
                var range_max = range_values[range_length-1];
                $.each(range_values, function(rk,rv){
                    if(rk==0)
                        rk='min';
                    else if(rk==range_length-1)
                        rk='max';
                    else
                        rk = ((rv-range_min)/(range_max-range_min)*100).toFixed(2)+'%';
                    range_json[rk] = [rv];
                });
                $.extend(params,{snap: true, range: range_json});
                if(stfacetdsearchkits.tooltips==1){
                    var rangeslider_fomater = {'to': function( value ){return value !== undefined && rangeslider_prefix+value+rangeslider_suffix;}, 'from': Number};
                    $.extend(params,{tooltips: [ rangeslider_fomater, rangeslider_fomater ]});
                }
            }else{
                has_error = true;
            }
        }
        if(prestashop.language.is_rtl=='1')
            $.extend(params,{direction: 'rtl'});

        if(has_error)
            return;
        noUiSlider.create(slide[0], params);
        slide[0].noUiSlider.on('update', function( values, handle ){
            var formated_value = '';
            if(k=='rangeslider'){
                $.each(range_values, function(rk,rv){
                    if(values[handle]==rv){
                        formated_value =  rv;
                        return false;
                    }
                });
                if(!formated_value)
                    formated_value = values[handle];
                formated_value = rangeslider_prefix+formated_value+rangeslider_suffix;
            }else{
                formated_value =  stfsk.formatPrice(values[handle],k);
            }
            if(stfacetdsearchkits.with_inputs==1 && k!='rangeslider')
                inputValues[handle].val(formated_value);
            else
                snapValues[handle].html(formated_value);
        });
        var temp_min = '';
        var temp_max = '';
        slide[0].noUiSlider.on('start', function(values, handle){
            temp_min = values[0];
            temp_max = values[1];
        });
        slide[0].noUiSlider.on('set', function(values, handle){
            var facet_url = '';
            if(values[0]!=temp_min || values[1]!=temp_max){
                facet_url += url;
                if(k=='rangeslider'){
                    $.each(range_values, function(rk,rv){
                        if(rv>=values[0] && rv<=values[1])
                            facet_url += '-'+rangeslider_prefix+rv+rangeslider_suffix;
                    });
                }else{
                    facet_url += '-'+values[0]+'-'+values[1];
                }
            }
            if(facet_url)
                prestashop.emit('updateFacets', facet_url);
        });
    },
    'fomater': {
        'weight' : {'to': function( value ){return value !== undefined && stfsk.formatPrice(value.toFixed(stfsk.jiazhong['weight'].format.decimals),'weight');}, 'from': Number},
        'price' : {'to': function( value ){return value !== undefined && stfsk.formatPrice(value.toFixed(stfsk.jiazhong['price'].format.decimals),'price');}, 'from': Number}
    },
    learnFormat : function(jiazhong){
        var reg = new RegExp("^([^\\d]*).*?([^\\d]*)$");
        var sample = stfacetdsearchkits.sample[jiazhong];
        var new_sample = stfacetdsearchkits.sample[jiazhong];
        var res = reg.exec(sample);
        if(res){
            if(res[1]){
                stfsk.jiazhong[jiazhong].format.prefix = res[1];
                new_sample = new_sample.replace(res[1],'');
            }
            if(res[2]){
                stfsk.jiazhong[jiazhong].format.suffix = res[2];
                new_sample = new_sample.replace(res[2],'');
            }
        }
        var reg = /([^\d])?(\d+)/g;
        var match;
        while (match = reg.exec(new_sample)) {
            stfsk.jiazhong[jiazhong].format.symbles.unshift({'change':(''+match[2]).length,'symble':match[1]?match[1]:''})
        }
        if(jiazhong=='weight'){
            var reg = new RegExp("[123456]",'g');
            var d_arr = sample.match(reg);
            stfsk.jiazhong[jiazhong].format.decimals = d_arr.length-6;
        }else{
            var reg = new RegExp("[123]",'g');
            var d_arr = sample.match(reg);
            stfsk.jiazhong[jiazhong].format.decimals = d_arr.length-3;
        }
    },
    'formatPrice' : function(value,jiazhong){
        if(!stfsk.jiazhong[jiazhong].format.symbles.length)
            return value;
        if(value==0)
            return stfsk.jiazhong[jiazhong].format.prefix+'0'+stfsk.jiazhong[jiazhong].format.suffix;
        if(jiazhong=='weight')
        {
            if(stfsk.jiazhong.weight.format.decimals)
                value = parseFloat(value).toFixed(stfsk.jiazhong.weight.format.decimals);
            else
                value = parseInt(value,10);
        }
        /*else
            value = value.toFixed(steco_payment.format.decimals);*/
        value = value.toString().replace(/([^\d]+)/g,'');
        var price_arr = value.split('').reverse();

        var price_new = '';
        var index = 0;
        $.each(stfsk.jiazhong[jiazhong].format.symbles, function(k,v){
            for (var i = 0; i < v.change; i++) { 
                if(index<price_arr.length)
                    price_new = price_arr[index++]+price_new;
                else
                    break;
            }
            if(index<price_arr.length)
                price_new = v.symble+price_new;
        });
        if(index<price_arr.length)
            for (var j=index; j < price_arr.length; j++) {
                price_new = price_arr[j]+price_new;
            }
        return stfsk.jiazhong[jiazhong].format.prefix+price_new+stfsk.jiazhong[jiazhong].format.suffix;
    },
    'deFormatPrice' : function(value,jiazhong){
        value = value.toString().replace(stfsk.jiazhong[jiazhong].format.prefix,'');
        value = value.replace(stfsk.jiazhong[jiazhong].format.suffix,'');
        if(stfsk.jiazhong[jiazhong].format.decimals)
        {
            var value_arr = value.split(stfsk.jiazhong[jiazhong].format.symbles[0].symble);
            if(value_arr.length>1){
                var decimal = value_arr.pop();
                value = value_arr.join('').replace(/([^\d]+)/g,'')+'.'+decimal;
            }else{
                value = value.replace(/([^\d]+)/g,'');
            }
        }
        else
            value = value.replace(/([^\d]+)/g,'');
        return value;
    }
};