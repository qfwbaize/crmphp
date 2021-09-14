<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Menu extends Model
{
    //
    protected $deleteTime = 'delete_time';
    protected $name = "system_menu";

    /**
     * 根据角色ID获取授权节点
     * @param $authId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAuthorizeNodeListByAdminId($authId)
    {


        $systemNode = new Menu();
        $nodelList = $systemNode
            ->where('status', '1')
            ->field('id,pid,name,status,path,href')
            ->select()
            ->toArray();

        $newNodeList = [];
        $datas = [];
        foreach ($nodelList as $vo) {
            $datas[] = $vo['path'];


        }
        $newNodeList['routes'] = $datas;
        return $newNodeList;
    }

    /**
     * 根据角色ID获取授权节点
     * @param $authId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAuthorizeNodeListByAdminmenuId($authId)
    {

        $checkNodeList = (new AuthNode())
            ->where('auth_id', $authId)
            ->column('node_id');
        $as = [];
        foreach ($checkNodeList as $k => $v) {
            $as[] = $v;
        }
        $data = join(',', $as);

        $systemNode = new Menu();
        $nodelList = $systemNode
            ->whereIn('id', $data)
            ->where('status', '1')
            ->field('id,pid,name,status,path,href')
            ->select()
            ->toArray();

        $newNodeList = [];
        $datas = [];
        foreach ($nodelList as $vo) {
            $datas[] = $vo['path'];


        }
        $newNodeList['routes'] = $datas;
        return $newNodeList;
    }

}
