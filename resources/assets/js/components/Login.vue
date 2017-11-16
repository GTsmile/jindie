<template>
    <div class="loginPage">
        <h2>用户登录</h2>
        <el-form class="loginFrom">

            <el-input type="text" id="user" class="input" v-model="formName.user" @blur="inputBlur('user',formName.user)"
                      placeholder="请输入用户名" icon="FontAwesome ion-person"></el-input>

            <el-input type="password" id="password" class="input" v-model="formName.password" @blur="inputBlur('password',formName.password)"
                      placeholder="请输入密码" icon="FontAwesome ion-key" v-on:click="showPassword()"></el-input>

            <el-input type="text" id="checkCode" class="input" v-model="formName.checkCode" @blur="inputBlur('checkCode',formName.checkCode)"
                      placeholder="验证码"></el-input>

            <img  @click="getImg" alt="验证码" src="" title="刷新图片" width="100" height="40" id="img" border="0">


            <div id="errorTrip">
                {{formName.error}}
            </div>

            <div  class="loginButton">

                <br>
                <el-button type="primary" @click="submitForm('formName')" class="submit">提交</el-button>
                <!--<el-button @click="resetForm">重置</el-button>-->
            </div>

        </el-form>              
    </div>
</template>

<script> 
    export default {
        name: '',
        data () {
            return {
                formName: {//表单中的参数
                    user: '',
                    error: '',
                    password: '',
                    checkCode: '',

                },
                beShow: false//传值给父组件
            }           
        },
        methods: {
            resetForm:function(){
                this.formName.user = '';
                this.formName.error = '';
                this.formName.password = '';
            },
            submitForm:function(formName){
                var username = this.formName.user;
                var password = this.formName.password;
                var self=this;

                axios.post('/login',{
                    'username':username,
                    'password':password

                })
                 .then(function(response){
                     console.log(response.data);
//                    if(response.data.code == 0){
//                        self.$router.push('/admin');
//                        console.log(response.data);
//                    }
                })
                .catch(function(response){

                })
            },
            inputBlur:function(errorItem,inputContent){
                if (errorItem === 'user') {
                    if (inputContent === '') {
                        this.formName.error = '请输入用户名'
                    }else{
                        this.formName.error = '';
                    }
                }else if(errorItem === 'password'){
                    if (inputContent === '') {
                        this.formName.error = '请输入密码'
                    }else{
                        this.formName.error = '';
                    }
                }else if(errorItem === 'checkCode'){
                    if(inputContent === ''){
                        this.formName.error = '请输入验证码'
                    }else{
                        this.formName.error = '';
                    }
                }
            },
            getImg(){
                axios.get('index/captcha/'+Math.random())
                .then(function(res){
                    console.log(res.data)
                    $("img").attr("src",res.data);
                })
                .catch(function(err){
                    console.log(err);
                 })
            },
            showPassword(){

            }
        },
        mounted(){
            this.getImg();
        }
    }
</script>

<style scoped>
    body{
        margin: 0;
        padding: 0;
    }
    .dialog{
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.8);
    }

    .loginPage{
        display: flex;
        flex-direction: column;
        justify-content:center;
        width: 350px;
        min-height: 300px;
        padding: 15px 30px 20px;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: rgb(255,255,255);
        opacity: 0.9;
        -moz-box-shadow:2px 2px 11px #333333;
        -webkit-box-shadow:2px 2px 11px #333333;
        box-shadow:2px 2px 11px #333333;
    }
    .loginPage h2{
        align-self:center;
    }

    .loginPage .input{
        margin-top: 10px;
        margin-bottom: 5px;
    }
    .loginPage .loginButton{
        width: inherit;
        margin-top: 20px;
        display: flex;
        justify-content: space-around;
    }
    .loginPage .loginFrom{
        position: relative;
    }
    .submit{
        width: 100%;
    }
    #checkCode{
        width: 160px;
    }
    #img{
        border-radius: 5px;
        position: absolute;
        top: 110px;
        right: 2px;
    }
    #errorTrip{
        height: 6px;
        padding-left: 10px;
        color: red;

    }
</style>