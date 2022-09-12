<div class="card">
    <div class="card-header">
        <div class="card-title">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">% Ключевых слов в ТОП</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Средняя позиция</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">Распределение по ТОП-100</a></li>
            </ul>
        </div>

        <div class="float-right">
            <select class="custom-select" id="chartFilterPeriod">
                <option value="days" selected>дням</option>
                <option value="weeks">неделям</option>
                <option value="month">месяцам</option>
            </select>
        </div>
    </div>

    <div class="card-body" style="position: relative">

        <div class="progress-spinner">
            <img src="/img/1485.gif" style="width: 50px; height: 50px;">
        </div>

        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="chart" style="position: relative; height:40vh; width:100%">
                    <canvas id="topPercent"></canvas>
                </div>
            </div>

            <div class="tab-pane" id="tab_2">
                <div class="chart" style="position: relative; height:40vh; width:100%">
                    <canvas id="middlePosition"></canvas>
                </div>
            </div>

            <div class="tab-pane" id="tab_3">
                <div class="chart" style="position: relative; height:40vh; width:100%">
                    <canvas id="distributionByTop"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
