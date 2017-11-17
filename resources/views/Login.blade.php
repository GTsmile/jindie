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
        .login_div{
            width:500px ;
            height:400px;
            background-color: #fff;
            border-radius: 6px;
            margin: 0 auto;
            margin-top: 8%;
            box-shadow: 1px 1px 1px 1px #cccccc;

        }
        .in{
            height: 35px;
            margin: 7px;
            width: 100%;
            padding-left: 5px;
            border-radius: 5px;
            border: 1px solid #cccccc;
        }
        form{
            width: 323px;
            text-align: center;
            padding: 20px 0;
        }
        #submit{
            background-color:RGB(56,121,217);
            height: 40px;
            font-size: 17px;
        }
        .captcha{
            float: left;
            height: 34px;
            margin: 5px 0;
            padding-left: 5px;
            width: 61%;
            border-radius: 5px;
            border: 1px solid #cccccc;
            margin: 7px;
        }
        #Captcha_img{
            margin-top: 8px;
            border-radius: 4px
        }
        *{
            margin: 0 auto;
        }
        .point{
            height: 21px;
            float: left;
            margin-left: 10px;
            color: red;
        }

    </style>
</head>
<body>
   <div class="login_div">
        <form>
            <h1>系统登录</h1>

            <p>
                <input type="text" name="username" id="name"  placeholder="请输入用户名" class="in" value="" required="required"/>
            </p>
            <p class="point"></p>
            <p>
                <input type="text" name="password" id="password"  placeholder="请输入密码" class="in" value="" required>
            </p>
           <p class="point"></p>
            <div style="clear: both"></div>
            <p >
                <input type="text" name="Captcha" class="captcha" placeholder="请输入验证码"id="Captcha" required>
                <a id="code_A"><img src="http://cgz.marchsoft.cn/captcha/1"id="Captcha_img"></a>
            </p>
            <div style="clear: both"></div>
            <p class="point"></p>
            <p>
                <input type="button" name="submit" value="登录" class="in" id="submit" onclick="submitForm()">
            </p>
            <p>{{csrf_field()}}</p>
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
                url:"/login",
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
