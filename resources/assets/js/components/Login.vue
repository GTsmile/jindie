<template>
    <div class="loginPage">
        <h1>登录</h1>
        <el-form>
            <el-form-item label="用户名">
                <el-input type="text" id="user" v-model="formName.user" @blur="inputBlur('user',formName.user)"></el-input>
                <p>{{formName.userError}}</p>
            </el-form-item>
            <el-form-item label="密码">
                <el-input type="password" id="password" v-model="formName.password" @blur="inputBlur('password',formName.password)"></el-input>
                <p>{{formName.passwordError}}</p>
            </el-form-item>
            <el-button type="primary" @click="submitForm('formName')">提交</el-button>
            <el-button @click="resetForm">重置</el-button>
        </el-form>              
    </div>
</template>

<script>
import Axios from 'axios'
    export default {
        name: '',
        data () {
            return {
                formName: {//表单中的参数
                    user: '',
                    userError: '',
                    password: '',
                    passwordError: '',
                },
                beShow: false//传值给父组件
            }           
        },
        methods: {
            resetForm:function(){
                this.formName.user = '';
                this.formName.userError = '';
                this.formName.password = '';
                this.formName.passwordError = '';
            },
            submitForm:function(formName){
                var username = this.formName.user;
                var password = this.formName.password;
                var self=this;
                axios.post('/login',{
                    'username':username,
                    'password':password
                }).then(function(res){
                    if(res.data=="true"){
                        console.log(res.data);
                        self.$router.push('/admin');
                    }
                })
                .catch(function(){

                })
            },
            inputBlur:function(errorItem,inputContent){
                if (errorItem === 'user') {
                    if (inputContent === '') {
                        this.formName.userError = '用户名不能为空'
                    }else{
                        this.formName.userError = '';
                    }
                }else if(errorItem === 'password') {
                    if (inputContent === '') {
                        this.formName.passwordError = '密码不能为空'
                    }else{
                        this.formName.passwordError = '';
                    }
                }
            }
        }
    }
</script>

<style scoped>
    html,body{
        margin: 0;
        padding: 0;
        position: relative;
    }
    .dialog{
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,.8);
    }
    .loginPage{
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -230px;
        margin-left: -175px;
        width: 350px;
        min-height: 300px;
        padding: 30px 20px 20px;
        border-radius: 8px;
        box-sizing: border-box;
    }
    .loginPage p{
        color: red;
        text-align: left;
    }
</style>