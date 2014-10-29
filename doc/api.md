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
* 返回：json

        成功
        {
            "data": {
                "uid": 34,
                "nickname": "路人123490",
                "kdou": 240,
                "token": "6Bxuk0F3FOg9d6YUTIISIl5xPY0=-MS4w-z...",
                "expired": 1800
            }
        }
        失败
        {
            "error": {
                "msg": '邮箱名不合法',
                "code": 1800
            }
        }
* 示例：
        /api/register?email=simonio@163.com&password=123456&uuid=hdjghur45hj&username=kandouwo

----
##登录
* HTTP：POST
* 认证：--
* URI：/api/login
* 参数：

        email 邮箱
        password 密码
        uuid 设备id
* 返回：json

        成功
        {
            "data": {
                "uid": 34,
                "nickname": "路人123490",
                "kdou": 240,
                "token": "6Bxuk0F3FOg9d6YUTIISIl5xPY0=-MS4w-z...",
                "expired": 1800
            }
        }
        失败
        {
            "error": {
                "msg": '邮箱名不合法',
                "code": 1800
            }
        }
* 示例：

        /api/login?email=simonio@163.com&password=123456&uuid=hdjghur45hj