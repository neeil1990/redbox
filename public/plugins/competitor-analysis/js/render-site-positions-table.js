function renderSitePositionsTable(response) {
    let table = document.createElement('table')
    table.id = 'positions'
    table.className = 'table table-bordered table-striped dataTable dtr-inline render'

    let thead = document.createElement('thead')
    let tr = document.createElement('tr')

    let domain = document.createElement('th')
    domain.textContent = getStringDomain()

    let percent = document.createElement('th')
    percent.textContent = getStringPercent()

    let avg = document.createElement('th')
    avg.textContent = getStringPosition()

    let tableBody = document.createElement('tbody')
    tableBody.id = 'positions-tbody'

    tr.appendChild(domain)
    tr.appendChild(percent)
    tr.appendChild(avg)
    thead.appendChild(tr)
    table.appendChild(thead)
    table.appendChild(tableBody)
    for (var position in response.positions) {
        let tr = document.createElement('tr')
        tr.className = 'render'
        let key = document.createElement('td')
        key.textContent = position

        let percent = document.createElement('td')
        percent.innerHTML = response.positions[position]['percent'] + '% ' + '<span class="text-muted">(' + response.positions[position]['count'] + ')</span>'

        let avg = document.createElement('td')
        avg.textContent = response.positions[position]['avg']
        tr.appendChild(key)
        tr.appendChild(percent)
        tr.appendChild(avg)
        tableBody.appendChild(tr)
    }
    document.querySelector('.positions.mt-5').appendChild(table)

    $('.positions').show()

    $('#positions').DataTable({
        "order": [[2, "asc"]],
    });
}
