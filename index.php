<?php

/**
 * Class Import
 * @param PHP抓取网页新闻数据并导出Excel
 * @autho 502236288@qq.com
 */
    class Import{
        /**
         * filterUrl
         * 获取模型
         * @access public
         * @param mixed $url 要抓取的网站的地址
         * @return []
         */
        public function filterUrl ( ) {
            $arr = [];
            $dataArr = [];
            for($i=1;$i<5;$i++){
                $url = 'https://www.zhaoshiji.com/Article/index.html?p='.$i;
                $output = self::sendCurl( $url );
                $data = substr($output,strpos($output,'<div id="connewsmenu1">'));
                $ul = explode( '<div class="news_item-list">',$data );
                unset($ul[0]);
                $ul = array_values($ul);
                foreach($ul as $key=>$value){
                    preg_match("/<p.*>(.*)<\/p>/",$value,$match);
                    $arr[$key]['title'] = $match[1];
                    $arr[$key]['desc'] = '这是一个测试数据';
                }
                $dataArr = array_merge($dataArr,$arr);
            }
            $this->import_excel($dataArr);
        }
        private static function sendCurl ( $url ) {
            $curl = curl_init();
            curl_setopt( $curl,CURLOPT_URL,$url );
            curl_setopt( $curl,CURLOPT_RETURNTRANSFER,true );
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//规避证书
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 防止302 盗链
            $output = curl_exec( $curl );
            curl_close( $curl );
            return $output;
        }

        public function import_excel($arr){
            $list = $arr;
            //2.加载PHPExcle类库
            vendor('PHPExcel.PHPExcel');
            //3.实例化PHPExcel类
            $objPHPExcel = new \PHPExcel();

            //4.激活当前的sheet表
            $objPHPExcel->setActiveSheetIndex(0);
            //5.设置表格头（即excel表格的第一行）
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '标题')
                ->setCellValue('B1', '描述')
            ;
            //设置A列水平居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            //设置单元格宽度
            //6.循环刚取出来的数组，将数据逐一添加到excel表格。
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(100);
            $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);

            for($i=0;$i<count($list);$i++){
                $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$list[$i]['title']);//ID
                $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$list[$i]['desc']);//标签码

            }
            //7.设置保存的Excel表格名称
            $filename = '企业名单'.date('ymd',time()).'.xls';
            //8.设置当前激活的sheet表格名称；
            $objPHPExcel->getActiveSheet()->setTitle('产品名单');
            //9.设置浏览器窗口下载表格
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="'.$filename.'"');
            //生成excel文件
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            //下载文件在浏览器窗口
            $objWriter->save('php://output');
            exit;
        }

}
?>