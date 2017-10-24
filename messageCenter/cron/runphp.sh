#!/bin/bash
phpdir=/usr/local/webserver/php/bin
errordir=/www/code/log/
second01=0
second05=0
i=0

phpexec(){
    if [ -e $1 ]
    then
        $phpdir/php $1 >> /dev/null 2>&1 &
    else
       # echo $1" 脚本不存在\n" >> $errordir/shell_error.log
    fi
}


while [ $i -le 65 ]
do
    if [ $second01 == 1 ]
    then
        # 1秒执行一次的脚本放在这里




        second01=-1
    fi
    second01=`expr $second01 + 1`

    if [ $second05 == 5 ]
    then
        # 5秒执行一次的脚本放在这里
        phpexec ./kuaidi_script.php
        phpexec ./mail_script.php
        phpexec ./phone_script.php

        second05=-1
    fi
    second05=`expr $second05 + 1`

echo $i
    i=`expr $i + 1`
    sleep 1
done

# 1分钟执行一次的脚本放在这里