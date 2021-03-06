<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org 
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
// +----------------------------------------------------------------------


namespace app\common\controller;


use app\BaseController;
use think\facade\Cache;
use think\Request;
use think\facade\Env;
use think\Model;
use think\facade\Db;
use EasyAdmin\tool\CommonTool;
use TCPDF;

/**
 * Class AdminController
 * @package app\common\controller
 */
class AdminController extends BaseController
{

    use \app\common\traits\JumpTrait;

    /**
     * 当前模型
     * @Model
     * @var object
     */
    protected $model;

    /**
     * 字段排序
     * @var array
     */
    protected $sort = [
        'id' => 'desc',
    ];

    /**
     * 允许修改的字段
     * @var array
     */
    protected $allowModifyFields = [
        'status',
        'sort',
        'remark',
        'is_delete',
        'is_auth',
        'title',
    ];

    /**
     * 不导出的字段信息
     * @var array
     */
    protected $noExportFields = ['delete_time', 'update_time'];

    /**
     * 下拉选择条件
     * @var array
     */
    protected $selectWhere = [];

    /**
     * 是否关联查询
     * @var bool
     */
    protected $relationSearch = false;

    /**
     * 模板布局, false取消
     * @var string|bool
     */
    protected $layout = 'layout/default';

    /**
     * 是否为演示环境
     * @var bool
     */
    protected $isDemo = false;


    /**
     * 初始化方法
     */
    protected function initialize()
    {

        parent::initialize();
        $this->layout && $this->app->view->engine()->layout($this->layout);

    }

    /**
     * 模板变量赋值
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return mixed
     */
    public function assign($name, $value = null)
    {
        return $this->app->view->assign($name, $value);
    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string $template
     * @param array $vars
     * @return mixed
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->app->view->fetch($template, $vars);
    }

    /**
     * 重写验证规则
     * @param array $data
     * @param array|string $validate
     * @param array $message
     * @param bool $batch
     * @return array|bool|string|true
     */
    public function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        try {
            parent::validate($data, $validate, $message, $batch);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return true;
    }

    /**
     * 构建请求参数
     * @param array $excludeFields 忽略构建搜索的字段
     * @return array
     */
    protected function buildTableParames($excludeFields = [])
    {
         $get = request()->get('', null, null);
        
        $page = isset($get['page']) && !empty($get['page']) ? $get['page'] : "1";
        $limit = isset($get['limit']) && !empty($get['limit']) ? $get['limit'] : "14";
        $page=intval($page);
        $limit=intval($limit);
        $filters = isset($get['filter']) && !empty($get['filter']) ? $get['filter'] : '{}';
        $ops = isset($get['op']) && !empty($get['op']) ? $get['op'] : '{}';
        // json转数组
        $filters = json_decode($filters, true);
        $ops = json_decode($ops, true);
        $where = [];
        $excludes = [];



        foreach ($filters as $key => $val) {
            if (in_array($key, $excludeFields)) {
                $excludes[$key] = $val;
                continue;

            }

            $op = isset($ops[$key]) && !empty($ops[$key]) ? $ops[$key] : '%*%';
            switch (strtolower($op)) {

                case '=':
                    $where[] = [$key, '=', $val];
                    break;
                case '%*%':
                    $where[] = [$key, 'LIKE', "%{$val}%"];
                    break;
                case '*%':
                    $where[] = [$key, 'LIKE', "{$val}%"];
                    break;
                case '%*':
                    $where[] = [$key, 'LIKE', "%{$val}"];
                    break;
                case 'in':
                    $where[] = [$key, 'in', "1,20,30,35,100"];
                    break;
                case 'notin':
                    $where[] = [$key, 'in', "40,50,60"];
                    break;
                case 'range':
                    $val=!empty($val) ? $val : '';
                    [$beginTime, $endTime] = explode(' - ', $val);
                    $where[] = [$key, '>=', strtotime($beginTime)];
                    $where[] = [$key, '<=', strtotime($endTime)];
                    break;
                case 'nick':

                    $users=Db::name('user')->field('id')->where('nickname','like',"%{$val}%")
                        ->select();
                    $user_id=[];

                    foreach ($users as $v){
                        $user_id[]=$v['id'];
                    }


                    $user=join(',',$user_id);

                    $where[] = ["uid", 'in', "{$user}"];


                    break;
                case 'paylawyer':

                    $users=Db::name('user')->field('id')->where('nickname','like',"%{$val}%")
                        ->select();
                    $user_id=[];

                    foreach ($users as $v){
                        $user_id[]=$v['id'];
                    }


                    $user=join(',',$user_id);



                    $where[]=['att_id','in',"{$user}"];


                    break;
                case 'order':

                    $users=Db::name('order')->field('id')->where('order_no',"$val")
                        ->select();

$order_id=[];
                    foreach ($users as $v){
                        $order_id[]=$v['id'];
                    }


                    $order=join(',',$order_id);

                    $where[] = ["source_id", '=', "{$order}"];
                    //dump($where);die;

                    break;
                case 'tel':

                    $users=Db::name('user')->field('id')->where('telphone','like',"%{$val}%")
                        ->select();
                    $user_id=[];
                    foreach ($users as $v){
                        $user_id[]=$v['id'];
                    }

                    $user=join(',',$user_id);

                    $where[] = ["uid", 'in', "{$user}"];


                    // dump($where);die;

                    break;
                case 'lawyer_tel':

                    $users=Db::name('user')->field('id')->where('telphone','like',"%{$val}%")
                        ->select();
                    $user_id=[];
                    foreach ($users as $v){
                        $user_id[]=$v['id'];
                    }
                    $user=join(',',$user_id);

                    $where[] = ["att_id", 'in', "{$user}"];


                    break;
                default:
                    $where[] = [$key, $op, "%{$val}"];
            }

        }
        //dump($where);die;
        return [$page, $limit, $where, $excludes];
    }

    /**
     * 下拉选择列表
     * @return \think\response\Json
     */
    public function selectList()
    {
        $fields = input('selectFields');
        $data = $this->model
            ->where($this->selectWhere)
            ->field($fields)
            ->select();
        $this->success(null, $data);
    }
    /**
     * 机构id
     * @return \think\response\Json
     */
    public function AdminId()
    {
        $sereact = Request()->header('token');

        $data=Cache::store('redis')->get("ONE_STAND:USER:login_token:$sereact");
        $data=json_decode($data,true);
        //dump($data["cid"]);die;
       return $data['cid'];

    }
    /**
     * 名片id
     * @return \think\response\Json
     */
    public function CardId()
    {
        $sereact = Request()->header('token');

        $data=Cache::store('redis')->get("ONE_STAND:USER:login_token:$sereact");
        $data=json_decode($data,true);
        //dump($data["cid"]);die;
        return $data['myCardId'];

    }
    /**
     * 系统通知
     * @return \think\response\Json
     */
    public function message($card_id,$msg,$data)
    {
        $post['content']=$msg;
        $post['type']=1;
        $messages = new \app\model\Messages();
        $message = new \app\model\Message();
        $save = $messages->save($post);
        $message_save=[
            'messages_id' => $messages->id,
            'from_id' => $this->AdminId(),
            'to_id' => $card_id
        ];

        $message->save($message_save);
    }
    /**
     * 生成pdf
     * @return \think\response\Json
     */
    public function pdf($html = '')
    {

        $pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // 设置打印模式
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 001');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        // 是否显示页眉
        $pdf->setPrintHeader(false);
        // 设置页眉显示的内容
        $pdf->SetHeaderData('logo.png', 60, 'marui.com', 'marui', array(0, 64, 255), array(0, 64, 128));
        // 设置页眉字体
        $pdf->setHeaderFont(array('dejavusans', '', '12'));
        // 页眉距离顶部的距离
        $pdf->SetHeaderMargin('5');
        // 是否显示页脚
        $pdf->setPrintFooter(true);
        // 设置页脚显示的内容
        $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
        // 设置页脚的字体
        $pdf->setFooterFont(array('dejavusans', '', '10'));
        // 设置页脚距离底部的距离
        $pdf->SetFooterMargin('10');
        // 设置默认等宽字体
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // 设置行高
        $pdf->setCellHeightRatio(1);
        // 设置左、上、右的间距
        $pdf->SetMargins('10', '10', '10');
        // 设置是否自动分页  距离底部多少距离时分页
        $pdf->SetAutoPageBreak(TRUE, '15');
        // 设置图像比例因子
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->setFontSubsetting(true);
        $pdf->AddPage();
        // 设置字体
        $pdf->SetFont('stsongstdlight', '', 14, '', true);
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $pdf->Output('example_001.pdf', 'I');
    }

}