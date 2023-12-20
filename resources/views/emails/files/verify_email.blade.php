<div
    style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;background-color:#f8fafc;color:#74787e;height:100%;line-height:1.4;margin:0;width:100%!important;word-break:break-word">
    <table
        style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;background-color:#f8fafc;margin:0;padding:0;width:100%">
        <tbody>
        <tr>
            <td style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box">
                <table
                    style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;margin:0;padding:0;width:100%">
                    <tbody>
                    <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;padding:25px 0;text-align:center">
                            <a href="https://lk.redbox.su"
                               style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;color:#bbbfc3;font-size:19px;font-weight:bold;text-decoration:none"
                               target="_blank">
                                Redbox
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;background-color:#ffffff;border-bottom:1px solid #edeff2;border-top:1px solid #edeff2;margin:0;padding:0;width:100%">
                            <table
                                style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;background-color:#ffffff;margin:0 auto;padding:0;width:570px">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;padding:35px">
                                        <h1 style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;color:#3d4852;font-size:19px;font-weight:bold;margin-top:0;text-align:left">
                                            @if($user->lang === 'ru')
                                                Привет, <a href="mailto:{{ $user->email }}"
                                                           target="_blank">{{ $user->email }}</a>.
                                            @else
                                                Hello, <a href="mailto:{{ $user->email }}"
                                                          target="_blank">{{ $user->email }}</a>.
                                            @endif
                                        </h1>
                                        @if($user->lang === 'ru')
                                            <p style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;color:#3d4852;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">
                                                Если вы не создавали учетную запись, никаких дальнейших действий не
                                                требуется.</p>
                                        @else
                                            <p style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;color:#3d4852;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">
                                                If you did not create an account, no further action is required.</p>
                                        @endif

                                        @if($user->lang === 'ru')
                                            <p style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;color:#3d4852;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">
                                                Ваш верификационный код: <b> {{ $code }}</b>
                                            </p>
                                        @else
                                            <p style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';box-sizing:border-box;color:#3d4852;font-size:16px;line-height:1.5em;margin-top:0;text-align:left">
                                                Verify Input Code: <b> {{ $code }}</b>
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<img style="display: none"
     src="https://lk.redbox.su/storage/audit/{{ str_replace('=', '0000', base64_encode($user->email)) }}.png" alt="">
