<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="http://cdn.static.runoob.com/libs/jquery/1.10.2/jquery.min.js"></script>
    <title>人员管理系统登录</title>
    <style>
        .form{
            width: 480px;
            height: 350px;
            background-color: #fff;
            border-radius: 6px;
            margin: 0 auto;
            margin-top: 8%;
            box-shadow: 1px 1px 1px 1px #cccccc;
        }
        .form input{
            margin-top: 16px;
        }
        .form h1{
            margin: 0;
        }
        .form span{
            font-size: 12px;
        }
        .in{
            height: 35px;
            margin: 7px;
            width: 60%;
            padding-left: 5px;
            border-radius: 5px;
            border: 1px solid #cccccc;
        }
        form{
            text-align: center;
            padding: 10px 0;
        }
        table{
            margin: 0 auto;
            width: 100%;
        }
        #submit{
            background-color:RGB(56,121,217);
        }
        .captcha{
            margin: 0;
            height: 30px;
            margin: 5px 0;
            padding-left: 5px;
            width: 39%;
            border-radius: 5px;
            border: 1px solid #cccccc;
        }
        #Captcha_img{
            margin-bottom: -12px;
            border-radius: 4px
        }
        #code_A{
            width: 100px;
        }
    </style>
</head>
<body>
   <div class="form">
        <form  >
               <h1>系统登录</h1>
            <table>
                <tr>
                    <td>
                        <input type="text" name="username" id="name"  placeholder="用户名" class="in" value="" required>
                    </td>
                </tr>
                <tr>
                    <td>
                    <input type="text" name="password" id="password"  placeholder="密码" class="in" value="" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="Captcha" class="captcha" id="Captcha">
                        <a id="code_A"><img src="http://cgz.marchsoft.cn/captcha/1"id="Captcha_img"></a>
                    </td>
                </tr>
                <tr><td><input type="button" name="submit" value="登录" class="in" id="submit" onclick="submitForm()"></td></tr>
                <tr><td>{{csrf_field()}}</td></tr>
            </table>
        </form>
   </div>
</body>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#code_A").click(function(){
        $("#Captcha_img").attr("src","http://cgz.marchsoft.cn/captcha/"+Math.random());
    });
    function submitForm() {
        $.ajax(
            {   type: "POST",
                url:"/check",
                data:{
                    "username":$("#name").val(),
                    "password":$("#password").val(),
                    "checkcode":$("#Captcha").val(),
                    },
                dataType: "json",
                success:function (response) {
                if(response.data.code==0){
                    window.location.reload()
                }else {}

                },
                error:function (response) {
                    console.log(11)
                }
            })
    }
</script>
</html>
