<?php


    namespace app\service;



    use think\facade\Db;
    use think\facade\Config;

    /**
     * 系统日志表
     * Class SystemLogService
     * @package app\admin\service
     */
    class SystemLogService
    {

        /**
         * 当前实例
         * @var object
         */
        protected static $instance;

        /**
         * 表前缀
         * @var string
         */
        protected $tablePrefix;

        /**
         * 表后缀
         * @var string
         */
        protected $tableSuffix;

        /**
         * 表名
         * @var string
         */
        protected $tableName;

        /**
         * 构造方法
         * SystemLogService constructor.
         */
        protected function __construct()
        {
            $this->tablePrefix = Config::get('database.connections.mysql.prefix');
            $this->tableName = "{$this->tablePrefix}system_log";
            return $this;
        }

        /**
         * 获取实例对象
         * @return \app\service\SystemLogService|object
         */
        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new static();
            }
            return self::$instance;
        }


        /**
         * 保存数据
         * @param $data
         * @return bool|string
         */
        public function save($data)
        {
            $this->detectTable();
            Db::startTrans();
            try {
                Db::table($this->tableName)->insert($data);
                Db::commit();
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            return true;
        }

        /**
         * 检测数据表
         * @return bool
         */
        protected function detectTable()
        {
            Db::query("show tables like '{$this->tableName}'");

                return true;


        }

        public function getAllTableList()
        {

        }


    }