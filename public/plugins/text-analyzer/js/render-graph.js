$(document).ready(function () {
    var options = {
        animationEnabled: true,
        theme: "light2",
        data: [
            {
                type: "line",
                name: "Реальные значения",
                showInLegend: true,
                dataPoints: graph
            },
            {
                type: "line",
                name: "Идеальные значения",
                showInLegend: true,
                dataPoints: [
                    {x: 5, y: graph[0]['y']},
                    {x: 6, y: Math.round(graph[0]['y'] / 2)},
                    {x: 7, y: Math.round(graph[0]['y'] / 3)},
                    {x: 8, y: Math.round(graph[0]['y'] / 4)},
                    {x: 9, y: Math.round(graph[0]['y'] / 5)},
                    {x: 10, y: Math.round(graph[0]['y'] / 6)},
                    {x: 11, y: Math.round(graph[0]['y'] / 7)},
                    {x: 12, y: Math.round(graph[0]['y'] / 8)},
                    {x: 13, y: Math.round(graph[0]['y'] / 8)},
                    {x: 14, y: Math.round(graph[0]['y'] / 9)},
                    {x: 15, y: Math.round(graph[0]['y'] / 9)},
                    {x: 16, y: Math.round(graph[0]['y'] / 9)},
                    {x: 17, y: Math.round(graph[0]['y'] / 9)},
                    {x: 18, y: Math.round(graph[0]['y'] / 10)},
                    {x: 19, y: Math.round(graph[0]['y'] / 10)},
                    {x: 20, y: Math.round(graph[0]['y'] / 10)},
                    {x: 21, y: Math.round(graph[0]['y'] / 10)},
                    {x: 22, y: Math.round(graph[0]['y'] / 10)},
                    {x: 23, y: Math.round(graph[0]['y'] / 10)},
                    {x: 24, y: Math.round(graph[0]['y'] / 10)},
                    {x: 25, y: Math.round(graph[0]['y'] / 10)},
                ]
            }]
    };

    $("#chartContainer").CanvasJSChart(options);
    $(function () {
        if (typeof textWithoutLinks === 'object') {
            let a = arrayToObj(textWithoutLinks)
            $("#textWithoutLinks").jQCloud(a);
        }
        if (typeof linksText === 'object') {
            let c = arrayToObj(linksText)
            $("#links").jQCloud(c);
        }
        if (typeof textWithLinks === 'object') {
            let e = arrayToObj(textWithLinks)
            $("#textWithLinks").jQCloud(e);
        }
    });

    function arrayToObj(array) {
        let length;
        if (array.count >= 250) {
            length = 250
        } else {
            length = array.count
        }
        let a = [], b = {};
        for (let i = 0; i < length; i++) {
            b = array[i]
            a.push(b);
        }
        return a;
    }
});
