##Api 接口说明
---
##注册
* HTTP：POST
* 认证：--
* URI：/api/register
* 参数：

        email 邮箱
        password 密码
        uuid 设备id
        username kindle人的用户名(可选，带此参数时，password参数即为kindle用户的密码，否则为新用户的密码)
        kindleren kindle人账号申请注册（需同时提供参数：username，password）
        kidnleren_confirm kindle人账号确认注册（需同时提供参数：username）
* 返回：json

        成功申请kindle人账号注册：
        {
            "data": {
                "login": 1
            }
        }
        成功注册：
        {
            "data": {
                "uid": 34,
                "nickname": "路人123490",
                "kindle_dou": 240, // 确认kindle人账号注册时有用（kindle人的k豆值）
                "token": "6Bxuk0F3FOg9d6YUTIISIl5xPY0=-MS4w-z...",
                "expired": 1800
            }
        }
        失败:
        {
            "error": {
                "msg": 'Email has been registered.',
                "code": 1800
            }
        }
* 示例：
        
        普通账号注册：/api/register?email=simonio@163.com&password=123456&uuid=hdjghur45hj
        申请kindle人账号注册：/api/register?kindleren=true&username=kandouwo&password=kandouwo
        确认kindle人账号注册：/api/register?kindleren_confirm=true&username=kandouwo&password=kandouwo&email=simonio1024@163.com&&uuid=123
* 错误码：
        
        -1：申请kindle人账号注册的参数错误
        -2：无效的kindle人用户名或密码
        -3：确认kindle人账号注册的参数错误
        -4：注册参数错误
        -5：邮箱已经被注册

----
##登录
* HTTP：POST
* 认证：--
* URI：/api/login
* 参数：

        account 邮箱/手机号
        password 密码
        uuid 设备id
* 返回：json

        成功登录：
        {
            "data": {
                "uid": 34,
                "nickname": "路人123490",
                "sex": 男,
                "signature": "",
                "thumbnail": "",
                "thumbnail_big": "",
                "attend_date": "",
                "lastlogin_place": "",
                "readed_book_num": "",
                "download_book_num": "",
                "comment_num": "",
                "kindleren": "true", // kindle人账号用户为true，否则为false
                "kdou": 240, // 看豆窝的k豆
                "kindle_dou": 0, // kindle人的k豆
                "token": "6Bxuk0F3FOg9d6YUTIISIl5xPY0=-MS4w-z...",
                "expired": 1800
            }
        }
        失败
        {
            "error": {
                "msg": 'Invalid email or password.',
                "code": 1800
            }
        }
* 示例：

        /api/login?email=simonio@163.com&password=123456&uuid=hdjghur45hj
* 错误码：
        -1：参数错误
        0：已经登录
        -2：用户名或密码错误
        -3：邮箱格式错误
