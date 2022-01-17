<div class="col-md-12">

<p>
    <span class="text-muted"> Файл с примером разрешений: <code>resources/views/manage-access/partials/_content.blade.php</code></span>
</p>
<p>
    <span class="text-muted">
        Мы можем разрешить с помощью директивы: <code>&#64;can</code>
    </span>
    <pre>
        &#64;can('Show header')
                // some logic
        &#64;endcan
    </pre>
</p>

    <code>Show header</code>
    @can('Show header')
        <h1>Header</h1>
    @endcan

    <br />

    <code>Show content</code>
    @can('Show content')
        <h1>Content</h1>
    @endcan

    <br />

    <code>Show footer</code>
    @can('Show footer')
        <h1>Footer</h1>
    @endcan

</div>
