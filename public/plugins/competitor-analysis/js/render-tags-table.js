function renderTagsTable(metaTags) {
    $('.tag-analysis').show()
    let tbody = document.querySelector('#tag-analysis-tbody')
    for (let metaTag in metaTags) {
        let tr = document.createElement('tr')
        tr.className = 'render'
        let keyword = document.createElement('td')
        keyword.textContent = metaTag
        tr.appendChild(keyword)

        let title = document.createElement('td')
        if ('title' in metaTags[metaTag]) {
            let titleDiv = document.createElement('div')
            titleDiv.style.height = '260px'
            titleDiv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['title']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['title'][t] + '</span></div>'
            }
            titleDiv.innerHTML = content
            title.appendChild(titleDiv)
        }
        tr.appendChild(title)

        let h1 = document.createElement('td')
        if ('h1' in metaTags[metaTag]) {
            let h1DIv = document.createElement('div')
            h1DIv.style.height = '260px'
            h1DIv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['h1']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['h1'][t] + '</span></div>'
            }
            h1DIv.innerHTML = content
            h1.appendChild(h1DIv)
        }

        tr.appendChild(h1)

        let h2 = document.createElement('td')
        if ('h2' in metaTags[metaTag]) {
            let h2DIv = document.createElement('div')
            h2DIv.style.height = '260px'
            h2DIv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['h2']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['h2'][t] + '</span></div>'
            }
            h2DIv.innerHTML = content
            h2.appendChild(h2DIv)
        }
        tr.appendChild(h2)

        let h3 = document.createElement('td')
        if ('h3' in metaTags[metaTag]) {
            let h3DIv = document.createElement('div')
            h3DIv.style.height = '260px'
            h3DIv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['h3']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['h3'][t] + '</span></div>'
            }
            h3DIv.innerHTML = content
            h3.appendChild(h3DIv)
        }
        tr.appendChild(h3)

        let h4 = document.createElement('td')
        if ('h4' in metaTags[metaTag]) {
            let h4DIv = document.createElement('div')
            h4DIv.style.height = '260px'
            h4DIv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['h4']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['h4'][t] + '</span></div>'
            }
            h4DIv.innerHTML = content
            h4.appendChild(h4DIv)
        }
        tr.appendChild(h4)

        let h5 = document.createElement('td')
        if ('h5' in metaTags[metaTag]) {
            let h5DIv = document.createElement('div')
            h5DIv.style.height = '260px'
            h5DIv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['h5']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['h5'][t] + '</span></div>'
            }
            h5DIv.innerHTML = content
            h5.appendChild(h5DIv)
        }
        tr.appendChild(h5)

        let h6 = document.createElement('td')
        if ('h6' in metaTags[metaTag]) {
            let h6DIv = document.createElement('div')
            h6DIv.style.height = '260px'
            h6DIv.style.overflowX = 'auto'
            let content = ''
            for (let t in metaTags[metaTag]['h6']) {
                content += '<div>' + t + ': <span class="text-muted">' + metaTags[metaTag]['h6'][t] + '</span></div>'
            }
            h6DIv.innerHTML = content
            h6.appendChild(h6DIv)
        }
        tr.appendChild(h6)

        tbody.appendChild(tr)
    }

}
