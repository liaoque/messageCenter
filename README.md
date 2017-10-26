一套服务系统
提供 邮件, 快递, 支付, 短信, 微信公众平台的API调用所需的access_token, 微信模版推送

需要配置东西蛮多的

必须配置
config/config.local.database.php                  数据库, redis配置
-------------------------------------------------------------------
config/config.php                   #实际上该项目不会使用任何cookie
    cookiepre                                    cookie前缀需要配置
    cookiedomain                                     domain必须配置
-------------------------------------------------------------------
选配,用到哪个配哪个
class/AliyunClient.php CDN
                          配置 $accessKeyId和$accessKeySecret就好了
-------------------------------------------------------------------
config/config.local.path.php 项目域名,
   HOST_MESSAGE_CENTER                          该项目的域名或者ip
-------------------------------------------------------------------
config/config.sms.php                                  短信相关配置
-------------------------------------------------------------------
config/config.email.php                                邮件相关配置


常量配置
CDN_OPEN 会自动刷新CDN, 只支持阿里云的,该项目没有用使用该功能
SEND_PHONE_MES 短信开关
SEND_MAIL_MES 邮件开关


nginx配置
server {
    listen     80 ;
    server_name  127.0.0.1;
        access_log  日志路径/messageCenter_access.log;
        root  项目路径;
        location / {
            index  index.html index.htm index.php;
        }
        error_page   500 502 503 504  /50x.html;
        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
        rewrite ^/(.*) /index.php?$1&;
}

-------------------------------------------------------------------
sql 表说明
admin_nav                       栏目
admin_template                  权限模版表
admin_user                      管理员
以上三张表是后台管理的表, 目前该系统内没有集成后台管理系统, 所以作用不大


app                             应用表
app_template                    应用模版关联表
template                        模版表
log_mail                        邮件推送日志表
log_phone                       短信推送日志表

push_list                       app推送配置表(目前支持极光推送)
log_push                        app推送日志表

wx_applist                      微信平台帐号配置表
log_wxtemplate                  微信模版推送日志表


kuaidi_companylist              本地快递列表
kuaidi_otherapplist             三方快递平台帐号配置表
kuaidi_otherlist                三方快递列表
kuaidi_waybill                  快递单号基本数据表
kuaidi_data                     快递详细数据表
log_kuaidisubscribe             快递推送日志表


pay_otherlist                   三方支付平台帐号配置表
pay_order                       订单表
pay_refound                     退款订单表


-------------------------------------------------------------------
crontab
配置 runphp.sh, 每分钟跑一次


有问题可以联系我qq:844596330


















