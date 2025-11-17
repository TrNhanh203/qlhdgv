<h4><strong>Các chuẩn đầu ra của học phần (CLO)</strong></h4>

<table class="clo-table">
    <thead>
        <tr>
            <th>CĐR HP</th>
            <th>Nội dung CĐR</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($clos as $clo)
            <tr>
                <td>{{ $clo->code }}</td>
                <td>
                    {!! nl2br(e($clo->description)) !!}

                    @if ($clo->bloom_level)
                        <br>
                        <strong><em>Mức Bloom:</em></strong> {{ $clo->bloom_level }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
