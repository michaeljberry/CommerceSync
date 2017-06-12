function graph(bindToDiv, groupLabels, url, xAxisFormat, yAxisLabel, xFormat){
    var chartDetails = {
        bindto: bindToDiv,
        size: {
            height: 650,
            width: 1200
        },
        data: {
            url: url,
            mimeType: 'json',
            x : 'x',
            type: 'bar',
            groups: groupLabels
        },
        grid: {
            x: {
                show: true
            },
            y: {
                show: true
            }
        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    culling: {
                        max: 10
                    },
                    rotate: 90
                },
                height: 100
            },
            y: {
                label: yAxisLabel,
                position: 'outer-middle'
            }
        }
    };
    if(xFormat && !xAxisFormat){
        chartDetails.data.xFormat = "%Y-%m";
        chartDetails.data.types = {
            'Amazon-sales':'area-step',
            'Ebay-sales':'area-step',
            'Walmart-sales':'area-step',
            'Reverb-sales':'area-step',
            'BigCommerce-sales':'area-step'
        };
        chartDetails.data.axes = {
            "Amazon-unitsSold":"y2",
            "Ebay-unitsSold":"y2",
            "Walmart-unitsSold":"y2",
            "Reverb-unitsSold":"y2",
            "BigCommerce-unitsSold":"y2",
        };
        chartDetails.axis.y2 = {
            show: true
        };
    }else if(!xFormat && xAxisFormat){
        chartDetails.axis.x.tick.format = xAxisFormat;
    }
    var chart = c3.generate(chartDetails);
}