<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 14:02
 */
class DbMessageCenter_AdminTemplate extends Model
{
    const ALWAYS = 1;

    const READ = 1;

    const WRITE = 2;

    const ALL = 3;

    const DB_PREFIX = 'message_center.admin_template';

    private static $navsRules = array();

    private static $currentNav = null;

    public function __construct()
    {
        $this->setTableName(self::DB_PREFIX);
        $this->fileCache = Cache_File::getInstance();
        parent::__construct();
    }

    /**
     * 根据模版id获取模版数据
     * @param $id
     * @return array
     */
    public function getTemplateDate($id)
    {
        $result = $this->findOneOfCache(array('id' => $id));
        $adminNavs = array();
        if (!empty($result)) {
            if ($result == '[]') {
                return array();
            }
            $adminNav = DbWeb_AdminNav::getInstance();
            $adminNavs = explode(',', $result['navs']);
            $navs = array();
            foreach ($adminNavs as $v) {
                if (empty($v)) {
                    continue;
                }
                $value = explode('|', $v);
                $navs[$value[0]] = $value[1];
            }
            $condition = $id == self::ALWAYS && empty($navs) ? array() : array('id' => array_keys($navs));
            $adminNavs = $adminNav->findAllOfCacheWithYes($condition, self::getAdminNavsKey($id));
            foreach ($adminNavs as $k => $v) {
                $adminNavs[$k]['auth'] = $navs[$v['id']];
            }
        }
        return $adminNavs;
    }

    /**
     * 判定是否可执行
     * @param $id
     * @param $path
     * @param $actionFalg
     * @return bool
     */
    public function isRun($id, $path, $actionFalg)
    {

        if ($id == self::ALWAYS) {
            return true;
        }
        $path = strtolower($path);
        $result = $this->getTemplateDate($id);
        foreach ($result as $value) {
            if ($path == strtolower($value['src'])) {
                if ($actionFalg == self::WRITE) {
                    return $value['auth'] == self::ALL;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @param $path url路径
     * @param $id   模版id
     * @return bool
     */
    public function setCurrentNav($path, $id)
    {
        $path = strtolower($path);
//        $adminNav = Oa_AdminNav::getInstance();
        $adminNavs = $this->getTemplateDate($id);
        foreach ($adminNavs as $value) {
            if ($path == strtolower($value['src'])) {
                self::$currentNav = $value;
                return true;
            }
        }
        return false;
    }
    public function searchFilter($params)
    {
        $name = $params['name'];
        unset($params['name']);
        $search = parent::searchFilter($params);
        if ($name) {
            $userData = $this->find(array('name' => $name), 'id');
            if (!empty($userData)) {
                $search['id'] = $userData['id'];
            }
        }
        return $search;
    }

    public static function getCurrentNav()
    {
        return self::$currentNav;
    }

    public static function getNavRules()
    {
        return self::$navsRules;
    }

    /**
     * 缓存模版并返回
     * @param $id
     * @return array
     */
    public function getTemplateBranchOfCache($id)
    {
        $key = self::getTemplateBranchKey($id);
        $result = $this->fileCache->get($key);
//        $result = self::decode($result);
        if (empty($result)) {
            if ($result == '[]') {
                return array();
            }
            $adminNav = DbWeb_AdminNav::getInstance();
            $template = array();
            $result = $this->findOneOfCache(array('id' => $id));
            if (!empty($result)) {
                $navs = explode(',', $result['navs']);
                foreach ($navs as $v) {
                    if (empty($v)) {
                        continue;
                    }
                    $v = explode('|', $v);
                    self::$navsRules[$v[0]] = $v[1];
                }
                $condition = $id == self::ALWAYS && empty(self::$navsRules) ? array() : array('id' => array_keys(self::$navsRules));
                $result = $adminNav->findAllOfCacheWithYes($condition, self::getAdminNavsKey($id));
                $template = $adminNav->createBranch($result, $template);
                $this->fileCache->set($key, $template);
            }
        } else {
            $template = self::deCode($result);
        }
        return $template;
    }

    /**
     * 返回管理模版的所有navs，原始数据的key
     * @param $id
     * @return string
     */
    public static function getAdminNavsKey($id)
    {
        return self::DB_PREFIX . ':cacheTemplate:id:' . $id;
    }

    /**
     * 返回管理模版的所有navs，处理过的key
     * @param $id
     * @return string
     */
    public static function getTemplateBranchKey($id)
    {
        return self::DB_PREFIX . ':cacheTemplateBranch:id:' . $id;
    }

    /**
     * 删除 返回管理模版的所有处理过的navs缓存
     * @param $id
     * @return mixed
     */
    public function delTemplateBranch($id)
    {
        $key = self::getTemplateBranchKey($id);
        return $this->fileCache->del($key);
    }

    /**
     * 清空模版缓存
     */
    public function clear()
    {
        $result = $this->findAll(array(), 'id');
        $adminNav = DbWeb_AdminNav::getInstance();
        foreach ($result as $v) {
            $id = $v['id'];
            $this->delTemplateBranch($id);
            $adminNav->delFindAllOfCacheWithYes(array(), self::getAdminNavsKey($id));
            $this->delFindOneOfCache(array('id' => $id));
        }
    }

    public function findOneOfCache($condition = array())
    {
        $key = md5(self::enCode($condition));
        $result = $this->fileCache->proxyModelSearch($key, array($this, 'find'), array($condition));
        if ($result['navs'] == -1) {
            $adminNavs = DbWeb_AdminNav::getInstance();
            $navs = $adminNavs->findAll(array(), 'id');
            if (!empty($navs)) {
                $nvaIds = array();
                foreach ($navs as $v) {
                    $nvaIds[] = $v['id'] . '|1';
                }
                $result['navs'] = implode(',', $nvaIds);
                $this->fileCache->set($key, Model::enCode($result));
            }
        }
        return $result;
    }

    public function delFindOneOfCache($condition = array()){
        $key = md5(self::enCode($condition));
        return $this->fileCache->del($key);
    }


    public function delete($id)
    {
        $sql = "delete from " . self::DB_PREFIX . " where id={$id}";
        return $GLOBALS['mysql']->execute($sql);

    }


    public function createTableOnly()
    {
        $key = self::REDIS_KEY . 'lock:' . md5($this->getTableName());
        if ($this->fileCache->exists($key)) {
            return true;
        }
        $result = $this->createTable();
        if (!empty($result)) {
            $this->fileCache->set($key, 1);
        }
        return $result;
    }

    public function createTable()
    {
        $tableName = $this->getTableName();
        $sql = "CREATE TABLE if not exists {$tableName} (
                 `id`  int(11) NOT NULL AUTO_INCREMENT ,
				`name`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '权限模版名字' ,
				`description`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '权限模版扩展字段' ,
				`navs`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '导航id' ,
				PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB
            DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
            COMMENT='角色表, 权限模版表'
            ";
        return $this->getDb()->execute($sql);
    }

}