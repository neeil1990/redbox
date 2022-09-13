function renderTopSites(response) {
    $('.top-sites').show(300)
    let tbody = document.querySelector('#top-sites-body');
    for (var keyword in response.sites) {
        let tr = document.createElement('tr')
        tr.className = 'render'
        let td = document.createElement('td')
        td.textContent = keyword
        tr.appendChild(td)
        for (var items in response.sites[keyword]) {
            let td = document.createElement('td')

            let div = document.createElement('div')
            div.className = 'd-flex justify-content-between pb-5'

            let span = document.createElement('span')
            span.className = 'domain'
            span.onclick = function () {
                if ($(this).parent().parent().children('div').eq(1).is(':visible')) {
                    $(this).children('i').eq(0).css({
                        "transform": "rotate(0deg)"
                    })
                    $(this).parent().parent().children('div').eq(1).hide()
                } else {
                    $(this).children('i').eq(0).css({
                        "transform": "rotate(90deg)"
                    })
                    $(this).parent().parent().children('div').eq(1).show()
                }
            }

            let i = document.createElement('i')
            i.className = 'expandable-table-caret fas fa-caret-right fa-fw'

            let u = document.createElement('u')
            u.style.cursor = 'pointer'
            u.textContent = response.sites[keyword][items].doc.domain

            let div2 = document.createElement('div')
            div2.className = 'btn-group'

            let button = document.createElement('button')
            button.type = 'button'
            button.setAttribute('data-toggle', 'dropdown')
            button.setAttribute('aria-expanded', 'false')
            button.className = 'btn btn-tool dropdown-toggle'

            let i2 = document.createElement('i')
            i2.className = 'fas fa-external-link-alt'

            let div3 = document.createElement('div')
            div3.setAttribute('role', 'menu')
            div3.className = 'dropdown-menu dropdown-menu-left'

            let aUrl = document.createElement('a')
            aUrl.target = '_blank';
            aUrl.className = 'dropdown-item'
            aUrl.href = response.sites[keyword][items].doc.url
            aUrl.innerHTML = '<i class="fas fa-external-link-alt"></i> ' + stringGoToPage()

            let aDomain = document.createElement('a')
            aDomain.target = '_blank';
            aDomain.className = 'dropdown-item'
            aDomain.href = "https://" + response.sites[keyword][items].doc.domain
            aDomain.innerHTML = '<i class="fas fa-external-link-alt"></i> ' + stringGoToSite()

            let aTextAnalyse = document.createElement('a')
            aTextAnalyse.target = '_blank';
            aTextAnalyse.className = 'dropdown-item'
            let url = response.sites[keyword][items].doc.url
            aTextAnalyse.href = location.origin + "/text-analyzer/" + url.replace(/\\|\//g, 'abc')
            aTextAnalyse.innerHTML = '<i class="fas fa-external-link-alt"></i> ' + stringGoToAnalyse()

            let hiddenDiv = document.createElement('div')
            hiddenDiv.className = 'pl-1'
            hiddenDiv.style.marginTop = '-35px'
            hiddenDiv.style.display = 'none'

            if (response.sites[keyword][items].meta.title.join(' ')) {
                let title = document.createElement('div')
                title.className = 'text-info'
                title.textContent = 'Title:'

                let titleDiv = document.createElement('div')
                titleDiv.style.color = 'black'
                if (response.sites[keyword][items].meta.title.length > 0) {
                    titleDiv.textContent = response.sites[keyword][items].meta.title.join(' ')
                }

                title.appendChild(titleDiv)
                hiddenDiv.appendChild(title)
            }

            if (response.sites[keyword][items].doc.headline && typeof response.sites[keyword][items].doc.headline !== 'object') {
                let headline = document.createElement('div')
                headline.className = 'text-info'
                headline.textContent = 'Headline:'

                let headlineDiv = document.createElement('div')
                headlineDiv.style.color = 'black'
                headlineDiv.textContent = response.sites[keyword][items].doc.headline

                headline.appendChild(headlineDiv)
                hiddenDiv.appendChild(headline)
            }

            if (response.sites[keyword][items].meta.description.join(' ')) {
                let description = document.createElement('div')
                description.className = 'text-info'
                description.textContent = 'Description:'

                let descriptionDiv = document.createElement('div')
                descriptionDiv.style.color = 'black'
                descriptionDiv.innerHTML = response.sites[keyword][items].meta.description.join('<br>')

                description.appendChild(descriptionDiv)
                hiddenDiv.appendChild(description)
            }

            if (response.sites[keyword][items].meta.h1.join(' ')) {
                let h1 = document.createElement('div')
                h1.className = 'text-info'
                h1.textContent = 'H1:'

                let H1Div = document.createElement('div')
                H1Div.style.color = 'black'
                H1Div.innerHTML = response.sites[keyword][items].meta.h1.join('<br>')

                h1.appendChild(H1Div)
                hiddenDiv.appendChild(h1)
            }

            if (response.sites[keyword][items].meta.h2.join(' ')) {
                let h2 = document.createElement('div')
                h2.className = 'text-info'
                h2.textContent = 'H2:'

                let h2Div = document.createElement('div')
                h2Div.style.color = 'black'
                h2Div.innerHTML = response.sites[keyword][items].meta.h2.join('<br>')

                h2.appendChild(h2Div)
                hiddenDiv.appendChild(h2)
            }

            if (response.sites[keyword][items].meta.h3.join(' ')) {
                let h3 = document.createElement('div')
                h3.className = 'text-info'
                h3.textContent = 'H3:'

                let h3Div = document.createElement('div')
                h3Div.style.color = 'black'
                h3Div.innerHTML = response.sites[keyword][items].meta.h3.join('<br>')

                h3.appendChild(h3Div)
                hiddenDiv.appendChild(h3)
            }

            if (response.sites[keyword][items].meta.h4.join(' ')) {
                let h4 = document.createElement('div')
                h4.className = 'text-info'
                h4.textContent = 'H4:'

                let h4Div = document.createElement('div')
                h4Div.style.color = 'black'
                h4Div.innerHTML = response.sites[keyword][items].meta.h4.join('<br>')

                h4.appendChild(h4Div)
                hiddenDiv.appendChild(h4)
            }

            if (response.sites[keyword][items].meta.h5.join(' ')) {
                let h5 = document.createElement('div')
                h5.className = 'text-info'
                h5.textContent = 'H5:'

                let h5Div = document.createElement('div')
                h5Div.style.color = 'black'
                h5Div.innerHTML = response.sites[keyword][items].meta.h5.join('<br>')

                h5.appendChild(h5Div)
                hiddenDiv.appendChild(h5)
            }

            if (response.sites[keyword][items].meta.h6.join(' ')) {
                let h6 = document.createElement('div')
                h6.className = 'text-info'
                h6.textContent = 'H6:'

                let h6Div = document.createElement('div')
                h6Div.style.color = 'black'
                h6Div.innerHTML = response.sites[keyword][items].meta.h6.join('<br>')

                h6.appendChild(h6Div)
                hiddenDiv.appendChild(h6)
            }

            if (!(response.sites[keyword][items].meta.title.join(' ') &&
                response.sites[keyword][items].meta.description.join(' ') ||
                response.sites[keyword][items].meta.h1.join(' ')
            )) {
                let danger = document.createElement('span')
                danger.className = 'text-danger'
                danger.textContent = getErrorMessage()
                hiddenDiv.appendChild(danger)
            }

            let linkIcon = document.createElement('i')
            linkIcon.className = 'fas fa-external-link-alt'

            div3.appendChild(aUrl)
            div3.appendChild(aDomain)
            div3.appendChild(aTextAnalyse)

            span.appendChild(i)
            span.appendChild(u)
            div.appendChild(span)
            td.appendChild(div)
            td.appendChild(hiddenDiv)
            tr.appendChild(td)

            button.appendChild(i2)
            div2.appendChild(button)
            div2.appendChild(div3)
            div.appendChild(div2)
        }
        tbody.appendChild(tr);
    }

    $('.text-danger').parent().parent().css({
        'background': '#ffc107'
    })
}
