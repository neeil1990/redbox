function renderScannedSitesList(sites, avgCoveragePercent, count, hide, boostPercent) {
    $('.sites').show(300)
    let iterator = 1;
    let tbody = $('#scanned-sites-tbody')
    $.each(sites, function (key, value) {
        let site = value['site']

        let btnGroup =
            "<div class='btn-group'>" +
            "        <button type='button' data-toggle='dropdown' aria-expanded='false' class='text-dark btn btn-tool dropdown-toggle'>" +
            "            <i class='fas fa-external-link-alt'></i>" +
            "        </button> " +
            "       <div role='menu' class='dropdown-menu dropdown-menu-left'>" +
            "            <a target='_blank' class='dropdown-item' href='" + value['site'] + "'>" +
            "                <i class='fas fa-external-link-alt'></i> Перейти на посадочную страницу" +
            "           </a>" +
            "            <a target='_blank' class='dropdown-item' href='/redirect-to-text-analyzer/" + site.replaceAll('/', 'abc') + "'>" +
            "                <i class='fas fa-external-link-alt'></i> Перейти в текстовый анализатор" +
            "           </a>" +
            "            <span class='dropdown-item add-in-ignored-domains' style='cursor: pointer'" +
            "                  data-target='" + value['site'] + "'>" +
            "                <i class='fas fa-external-link-alt'></i>" +
            "                Добавить в игнорируемые домены" +
            "            </span>" +
            "           <span class='dropdown-item remove-from-ignored-domains' style='cursor: pointer'" +
            "                 data-target='" + value['site'] + "'>" +
            "               <i class='fas fa-external-link-alt'></i>" +
            "               Исключить из игнорируемых доменов" +
            "           </span>" +
            "        </div>" +
            "</div>";

        let noTop = ''
        let ignorBlock = ''
        let ignorClass = ''
        let className = ''
        let warning

        if (value['ignored']) {
            ignorBlock = "<div class='text-muted'>(игнорируемый домен)</div>"
            ignorClass = " ignored-site"
        }

        if (value['danger']) {
            warning = "<td class='bg-warning'>" +
                "   <span data-scroll='#ignoredDomains' class='scroll-to-ignored-list pointer'> Не удалось получить данные со страницы</span>"
                + ignorBlock +
                "</td>";
        } else {
            warning = "<td>" +
                "   <span data-scroll='#ignoredDomains' class='scroll-to-ignored-list pointer'> Страница успешно проанализирована </span>"
                + ignorBlock +
                "</td>"
        }

        if (value['mainPage']) {
            if (!value['inRelevance']) {
                noTop = "<span class='text-muted'>(сайт не попал в топ)</span>"
            }
            className = 'bg-my-site'
        } else if (value['equallyHost']) {
            className = 'bg-warning-elem'
        }

        var position

        if (!value['position']) {
            position = 'не попал в топ 100'
        } else {
            position = value['position']
        }

        console.log(value)
        console.log(value['width'])
        tbody.append(
            "<tr class='render" + ignorClass + "'>" +
            "<td data-order='" + iterator + "'>" + position + "</td>" +
            "<td data-order='" + iterator + "' style='max-width: 450px;' class='" + className + "'>" +
            "   <span class='analyzed-site' id='site-" + value['position'] + "'>" + value['site'] + "</span>"
            + noTop + btnGroup
            + "</td>" +
            "<td data-order='" + value['mainPoints'] + "'>" + value['mainPoints'] + " </td>" +
            "<td data-order='" + value['coverage'] + "'>" + value['coverage'] + "% </td>" +
            "<td data-order='" + value['coverageTf'] + "'>" + value['coverageTf'] + "% </td>" +
            "<td data-order='" + value['width'] + "'>" + value['width'] + "</td>" +
            "<td data-order='" + value['density']['densityMainPercent'] + "'>" + value['density']['densityMainPercent'] + "</td>" +
            "<td data-order='" + value['countSymbols'] + "'>" + value['countSymbols'] + "</td>" +
            warning +
            "</tr>"
        )
        iterator++
    });

    $(document).ready(function () {
        $('#scaned-sites').DataTable({
            "order": [[0, "asc"]],
            "pageLength": count,
            "searching": true,
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel'
            ]
        });
    });

    setTimeout(() => {
        $('#scaned-sites').wrap("<div style='width: 100%; overflow-x: scroll; max-height:90vh;'></div>")

        $('#scaned-sites_length').before(
            "    <div class='d-flex'>" +
            "        <div class='__helper-link ui_tooltip_w'>" +
            "            <div class='custom-control custom-switch custom-switch-off-danger custom-switch-on-success'>" +
            "                <input type='checkbox'" +
            "                       class='custom-control-input'" +
            "                       id='showOrHideIgnoredSites'" +
            "                       name='noIndex'>" +
            "                <label class='custom-control-label' for='showOrHideIgnoredSites'></label>" +
            "            </div>" +
            "        </div>" +
            "        <p>скрыть игнорируемые домены</p>" +
            "    </div>"
        )

        $('#showOrHideIgnoredSites').click(function () {
            if ($('.ignored-site').is(':visible')) {
                $('.ignored-site').hide()
            } else {
                $('.ignored-site').show()
            }
        });

        if (hide === 'yes') {
            $('#showOrHideIgnoredSites').trigger('click');
        }

        $('#scaned-sites_wrapper > .dt-buttons').after(
            "    <button class='btn btn-secondary ml-1' id='copySites' style='cursor: pointer'>" +
            "        Скопировать ссылки сайтов" +
            "    </button>"
        )

        $('#copySites').click(function () {
            let sites = ''
            $.each($('.analyzed-site'), function () {
                sites += $(this).html() + "\n"
            })
            const el = document.createElement('textarea');
            el.value = sites;
            el.setAttribute('readonly', '');
            el.style.position = 'absolute';
            el.style.left = '-9999px';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            let toastr = $('.toast-top-right.success-message.lock-word');
            toastr.show(300)
            $('#lock-word').html('Успешно скопировано')
            setTimeout(() => {
                toastr.hide(300)
            }, 3000)
        })
    }, 2000)

    $('.add-in-ignored-domains').click(function () {
        let url = new URL($(this).attr('data-target'))
        let textarea = $('.form-control.ignoredDomains')
        let string = textarea.val()
        if (!string.includes(url.hostname)) {
            let domain = (url.hostname).replace('www.', '')

            if (textarea.val().slice(-1) === "\n") {
                textarea.val(textarea.val() + domain + "\n")
            } else {
                textarea.val(textarea.val() + "\n" + domain + "\n")
            }

            let toastr = $('.toast-top-right.success-message.lock-word');
            toastr.show(300)
            $('#lock-word').html('Домен "' + domain + '" добавлен в игнорируемые')
            setTimeout(() => {
                toastr.hide(300)
            }, 3000)
        }
    });

    $('.remove-from-ignored-domains').click(function () {
        let url = new URL($(this).attr('data-target'))
        let textarea = $('.form-control.ignoredDomains')
        let string = textarea.val()
        if (string.includes(url.hostname)) {
            let domain = (url.hostname).replace('www.', '')
            textarea.val(textarea.val().replace(domain, ""))

            let toastr = $('.toast-top-right.success-message.lock-word');
            toastr.show(300)
            $('#lock-word').html('Домен "' + domain + '" удалён из игнорируемых')
            setTimeout(() => {
                toastr.hide(300)
            }, 3000)
        }
    });

    $('.scroll-to-ignored-list').on('click', function () {
        var el = $(this);
        var dest = el.attr('data-scroll');
        $('html').animate({
                scrollTop: $(dest).offset().top
            }, 500
        );
        return false;
    });

    $('#avgCoveragePercent').html(avgCoveragePercent.toFixed(3))
    $("#avgCoveragePercentInput").change(function () {
        let number = $('#avgCoveragePercent').html()
        if ($("#avgCoveragePercentInput").val() !== '') {
            number = Number(number)
            number = number + ((number / 100) * $('#avgCoveragePercentInput').val())
            let freshNumber = number.toFixed(3)
            $('#changedAvgPercent').html('(' + freshNumber + ')')

            var freshPercent
            $('#scanned-sites-tbody tr').each(function () {
                $(this).find('td').each(function (cell) {
                    if (cell == 3) {
                        let thisValue = Number($(this).html().replace('%', ''))
                        freshPercent = Math.min(thisValue / (freshNumber / 100), 100)
                        freshPercent = freshPercent.toFixed(3)
                    }
                    if (cell == 5) {
                        $(this).attr('data-order', freshPercent)
                        $(this).html(freshPercent)
                    }
                });
            });
        }
    });

    if (boostPercent) {
        $('#avgCoveragePercentInput').val(boostPercent)
        let number = $('#avgCoveragePercent').html()
        number = Number(number)
        number = number + ((number / 100) * boostPercent)
        let freshNumber = number.toFixed(3)
        $('#changedAvgPercent').html('(' + freshNumber + ')')

        var freshPercent
        $('#scanned-sites-tbody tr').each(function () {
            $(this).find('td').each(function (cell) {
                if (cell == 3) {
                    let thisValue = Number($(this).html().replace('%', ''))
                    freshPercent = Math.min(thisValue / (freshNumber / 100), 100)
                    freshPercent = freshPercent.toFixed(3)
                }
                if (cell == 5) {
                    $(this).attr('data-order', freshPercent)
                    $(this).html(freshPercent)
                }
            });
        });
    }

}
