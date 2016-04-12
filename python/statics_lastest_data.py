import time
import pymysql 
import datetime
import random
import os
import string
import random
import hashlib

def trip_str(old_str):
    new_str = old_str.strip().replace("\r","").replace("\n","").replace("\t","")
    return new_str

def get_uid(channel_id):
    channel_name = "channel"+str(channel_id)
    sql = "select uid from hz_user where name = %s"
    param = [channel_name]
    cursor.execute(sql,param)
    res = cursor.fetchone()
    if res is None:
        role_id=2
        curr_time = time.time()
        passport = hashlib.md5((str)(channel_name).encode('utf-8')).hexdigest()
        sql = "insert into hz_user(name,passport,role_id,register_time) values (%s,%s,%s,%s)"
        param = [channel_name,passport,role_id,curr_time]
        cursor.execute(sql,param)
        return int(cursor.lastrowid)
    else:
        return res[0]

def get_soft_id(softname):
    sql = "select id from hz_soft_market where softname = %s"
    param = [softname]
    cursor.execute(sql,param)
    return cursor.fetchone()

def insert_soft_id(softname):
    if softname is not None:
        version="1.0"
        sql = "insert into hz_soft_market(softname,softname_en,version) values (%s,%s,%s)"
        param = [softname,softname,version]
        cursor.execute(sql,param)
        return int(cursor.lastrowid) 

conn = pymysql.connect(host="10.10.96.56",user="root",passwd="Gegelovebaobei1314",db="fchao",charset="utf8")
cursor = conn.cursor()

current_year = int(time.strftime('%Y',time.localtime(time.time())))
current_month = int(time.strftime('%m',time.localtime(time.time())))
current_day = int(int(time.strftime('%d',time.localtime(time.time()))))
a = str(current_year)+"-"+str(current_month)+"-"+str(current_day)+" 00:00:00"
end_time=int(time.mktime(time.strptime(a,'%Y-%m-%d %H:%M:%S')))
start_time=end_time-900
#start_time=end_time-86400
#print(start_time)
#print(end_time)

lastDate = datetime.date.today() - datetime.timedelta(days=1)
lastDate_stamp = lastDate.strftime('%Y%m%d')
myfile_handle = open('/data/logs/api.fchao.me/access_'+lastDate_stamp+'.log')
#myfile_handle = open('/data/logs/api.fchao.me/access.log')
line_list= []
post_parm_list = []
while 1:
    line = myfile_handle.readline()
    if line:
        pass
        sql = ''
        channel_id  = 0
        system_version = ''
        soft_version = ''
        diskno = ''
        ip = '0'
        mac = ''
        explore = ''
        safesoft = ''
        tablename = ''
        md5str = ''
        ipmac = ''
        lockpage_id = 0
        softname_id = ''
        uid = 0
        timestr = ''
        softname = ''
            
        tmp_list = []
        tmp_list1 = []
        insert_params = []
        line = trip_str(line)
        line_list = line.split(" ")
        timestr = line_list[3].replace("[","")
        timestr = time.strptime(timestr,'%d/%b/%Y:%H:%M:%S')
        ctime  = (int)(time.mktime(timestr))
        post_parm = line_list[6].replace("/","").replace("?","")
        if post_parm != '' and post_parm is not None and line_list[6].find("softname") != -1 and ctime >= start_time and ctime < end_time:
            post_parm_list = post_parm.split("&")
            for x in post_parm_list:
                tmp_list = x.split("=")
                if tmp_list[0] == 'softname':
                    softname = tmp_list[1]
                    #tmp_list1 = softname.split("_")
                    #uid = tmp_list1[2]
                    #softname_id = get_soft_id(tmp_list1[0],tmp_list1[1])
                    softname_id = get_soft_id(softname)
                    if softname_id is not None:
                        softname_id = softname_id[0]
                    else:
                        softname_id = insert_soft_id(softname)
                elif tmp_list[0] == 'ip':
                     ip = tmp_list[1]
                     channel_id = tmp_list[1]
                     if channel_id is not None:
                         uid = get_uid(channel_id)
                elif tmp_list[0] == 'mac':
                    mac = tmp_list[1].replace("%0A","").replace("-","")
                elif tmp_list[0] == 'active':
                    active = tmp_list[1]

            if mac is not None and softname_id is not None and uid is not None and softname_id > 0 and uid > 0:
                ipmac = ip + mac
                '''
                print(softname_id)
                print(ip)
                print(mac)
                print(ctime)
                print(active)
                print(ipmac)
                print(uid)
                '''
                md5str = hashlib.md5((str)(uid).encode('utf-8')).hexdigest()
                tablename = "hz_user_relation_soft_"+md5str[:1]
                ctime_stamp = time.strftime('%Y-%m-%d', time.localtime(ctime))
                sql = "insert into "+tablename+"(softname_id,ctime,ip,mac,ipmac,active,uid,ctime_stamp) values (%s,%s,%s,%s,%s,%s,%s,%s)"
                insert_params = [softname_id,ctime,ip,mac,ipmac,active,uid,ctime_stamp]
                #print (insert_params)
                cursor.execute(sql,insert_params)
    else:
        break 
