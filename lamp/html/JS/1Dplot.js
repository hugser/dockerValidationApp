
var dataX = document.getElementById('plot1D').getAttribute('dataX').split(',').map(Number);
var dataXlabel = document.getElementById('plot1D').getAttribute('dataXlabel');
var dataY = document.getElementById('plot1D').getAttribute('dataY').split(',').map(Number);
var dataYlabel = document.getElementById('plot1D').getAttribute('dataYlabel');
var plotTitle = document.getElementById('plot1D').getAttribute('plotTitle');

var dataXY = dataX.map(function(v,i) {
    return [v, dataY[i]];
});

//alert(dataXlabel);
//console.log(dataXY);

// based on prepared DOM, initialize echarts instance
var myChart = echarts.init(document.getElementById('plot1D'));
        

var mytextStyle={
    Color: "double", 
    fontStyle: "normal",
    fontWeight: "normal", 
    fontFamily: "sans serif",
    fontSize:18
    };
// specify chart configuration item and data
var option = {
    backgroundColor: new echarts.graphic.RadialGradient(0.5, 0.5, 2, [{
        offset: 0,
        color: '#f5f5f5'
    }, {
        offset: 1,
        color: '#f5f5f5'
    }]),
    title: [ {
        top: '2%',
        left: 'center',
        text: plotTitle
    }],
    legend: {
        data: ['1', '2'],
        orient: 'vertical',
        x: 'right',
        y: 'center'
    },
xAxis: {
    type: 'value',
    nameLocation: 'middle',
    name: dataXlabel,
    nameGap:35,
    nameTextStyle:mytextStyle  
},
yAxis: {
    type: 'value',
    nameLocation: 'end',
    name: dataYlabel,
    nameTextStyle:mytextStyle  
},
series: [{
    name: '1',
    data: dataXY,
    type: 'line',
    smooth: true
},
{
    name: '2',
    data: dataXY,
    type: 'line',
    smooth: true
}]
};
        
                // use configuration item and data specified to show chart
myChart.setOption(option);


window.onresize = function() {
    myChart.resize();
  };


