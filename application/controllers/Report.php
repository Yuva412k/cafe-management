<?php

namespace app\application\controllers;

use app\application\core\Permission;
use app\core\Config;
use app\core\Controller;
use app\core\Session;

class Report extends Controller{

    public function beforeAction()
    {
        parent::beforeAction();

        $action = $this->request->param('action');

        $actions = ['showSalesReport','showSalesPaymentsReport','showPurchasePaymentsReport','showItemSalesReport','showSalesReturnReport','showPurchaseReport','showPurchaseReturnReport','showStockReport','showExpiredItemsReport'];
        $this->Security->requireAjax($actions);
        switch($action)
        {
            case 'showSalesReport': 
                $this->Security->config('validateForm',false);    
                // $this->Security->config('form',['fields'=>['unit_name','unit_id','unit_description']]);
                break;
            case 'showPurchaseReport': 
                $this->Security->config('validateForm',false);    
                break;
            case 'showPurchaseReturnReport': 
                $this->Security->config('validateForm',false);    
                break;
            case 'showSalesReturnReport': 
                $this->Security->config('validateForm',false);    
                break;
            case 'showStockReport': 
                $this->Security->config('validateForm',false);    
                break;
			case 'showItemSalesReport': 
				$this->Security->config('validateForm',false);    
				break;
			case 'showSalesPaymentsReport': 
				$this->Security->config('validateForm',false);    
				break;
			case 'showPurchasePaymentsReport': 
				$this->Security->config('validateForm',false);    
				break;
			case 'showExpiredItemsReport': 
				$this->Security->config('validateForm',false);    
				break;
        }
        $this->loadModel('reportModel');
    }
    
    public function purchase()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/purchasereport");
    }

    public function showPurchaseReport()
    {
        $res = $this->reportModel->showPurchaseReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td><a title='View Invoice' style='color:#f4f4f4' href='".PUBLIC_ROOT."purchase/invoice/".$data['purchase_id']."'>".$data['purchase_id']."</a></td>";
				echo "<td >".$data['purchase_date']."</td>";
				echo "<td>".$data['supplier_id']."</td>";
				echo "<td >".$data['supplier_name']."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['paid_amount'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'] - $data['paid_amount'],2)."</td>";
				echo "</tr>";
				$tot_grand_total+=$data['grand_total'];
				$tot_paid_amount+=$data['paid_amount'];
				$tot_due_amount+=($data['grand_total']-$data['paid_amount']);

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='5'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_grand_total,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_paid_amount,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_due_amount,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan=13 style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
		
    }

    public function purchasereturn()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/purchasereturnreport");
    }

    public function showPurchaseReturnReport()
    {
        $res = $this->reportModel->showPurchaseReturnReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo !empty($data['purchase_id']) ? "<td><a title='Return Raised Against this Invoice' style='color:#053bc4' href='".PUBLIC_ROOT."purchase/invoice/".$data['purchase_id']."'>".$data['purchase_id']."</a></td>" : '<td>-NA-</td>';
				echo "<td><a title='View Invoice' style='color:#053bc4' href='".PUBLIC_ROOT."purchaseReturn/invoice/".$data['return_id']."'>".$data['return_id']."</a></td>";
				echo "<td >".$data['return_date']."</td>";
				echo "<td>".$data['supplier_id']."</td>";
				echo "<td >".$data['supplier_name']."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['paid_amount'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'] - $data['paid_amount'],2)."</td>";
				echo "</tr>";
				$tot_grand_total+=$data['grand_total'];
				$tot_paid_amount+=$data['paid_amount'];
				$tot_due_amount+=($data['grand_total']-$data['paid_amount']);

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='5'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_grand_total,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_paid_amount,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_due_amount,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan=13 style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
    }

	public function purchasepayment()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/purchasepaymentsreport");
    }

    public function showPurchasePaymentsReport()
    {
        $res = $this->reportModel->showPurchasePaymentReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_payment=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td><a title='View Invoice' style='color:#f4f4f4' href='".PUBLIC_ROOT."purchase/invoice/".$data['purchase_id']."'>".$data['purchase_id']."</a></td>";
				echo "<td >".$data['payment_date']."</td>";
				echo "<td>".$data['supplier_id']."</td>";
				echo "<td >".$data['supplier_name']."</td>";
				echo "<td >".$data['payment_note']."</td>";
				echo "<td >".$data['payment_type']."</td>";
				echo "<td style='text-align:right'>".number_format($data['payment'],2)."</td>";
				echo "</tr>";
				$tot_payment+=$data['payment'];

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='7'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_payment,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan='7' style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
		
    }
    public function sales()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/salesreport");
    }

    public function showSalesReport()
    {
        $res = $this->reportModel->showSalesReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td><a title='View Invoice' style='color:#f4f4f4' href='".PUBLIC_ROOT."sales/invoice/".$data['sales_id']."'>".$data['sales_id']."</a></td>";
				echo "<td >".$data['sales_date']."</td>";
				echo "<td>".$data['customer_id']."</td>";
				echo "<td >".$data['customer_name']."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['paid_amount'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'] - $data['paid_amount'],2)."</td>";
				echo "</tr>";
				$tot_grand_total+=$data['grand_total'];
				$tot_paid_amount+=$data['paid_amount'];
				$tot_due_amount+=($data['grand_total']-$data['paid_amount']);

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='5'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_grand_total,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_paid_amount,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_due_amount,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan=13 style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
		
    }

	public function salesreturn()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/salesreturnreport");
    }

    public function showSalesReturnReport()
    {
        $res = $this->reportModel->showSalesReturnReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_grand_total=0;
			$tot_paid_amount=0;
			$tot_due_amount=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo !empty($data['sales_id']) ? "<td><a title='Return Raised Against this Invoice' style='color:#053bc4' href='".PUBLIC_ROOT."sales/invoice/".$data['sales_id']."'>".$data['sales_id']."</a></td>" : '<td>-NA-</td>';
				echo "<td><a title='View Invoice' style='color:#053bc4' href='".PUBLIC_ROOT."salesReturn/invoice/".$data['return_id']."'>".$data['return_id']."</a></td>";
				echo "<td >".$data['return_date']."</td>";
				echo "<td>".$data['customer_id']."</td>";
				echo "<td >".$data['customer_name']."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['paid_amount'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['grand_total'] - $data['paid_amount'],2)."</td>";
				echo "</tr>";
				$tot_grand_total+=$data['grand_total'];
				$tot_paid_amount+=$data['paid_amount'];
				$tot_due_amount+=($data['grand_total']-$data['paid_amount']);

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='5'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_grand_total,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_paid_amount,2)."</td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_due_amount,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan=13 style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
    }

	public function salespayment()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/salespaymentsreport");
    }

    public function showSalesPaymentsReport()
    {
        $res = $this->reportModel->showSalesPaymentReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_payment=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td><a title='View Invoice' style='color:#f4f4f4' href='".PUBLIC_ROOT."sales/invoice/".$data['sales_id']."'>".$data['sales_id']."</a></td>";
				echo "<td >".$data['payment_date']."</td>";
				echo "<td>".$data['customer_id']."</td>";
				echo "<td >".$data['customer_name']."</td>";
				echo "<td >".$data['payment_note']."</td>";
				echo "<td >".$data['payment_type']."</td>";
				echo "<td style='text-align:right'>".number_format($data['payment'],2)."</td>";
				echo "</tr>";
				$tot_payment+=$data['payment'];

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='7'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_payment,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan='7' style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
		
    }
    public function stock()
    {
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/stockreport");
    }

    public function showStockReport()
    {
        $res = $this->reportModel->showStockReport($_POST);
        if(!empty($res)){
            $i=0;
			$tot_stock_value=0;
			$tot_sales_price=0;
			$tot_sales_price=0;
			$tot_stock=0;
			foreach ($res as $data) {
				$stock_value = $data['sales_price'] * $data['stock_qty'];
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td>".$data['item_id']."</td>";
				echo "<td>".$data['item_name']."</td>";
				echo "<td style='text-align:right'>".number_format($data['sales_price'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['sales_price'],2)."</td>";
				echo "<td>".$data['stock_qty']."</td>";
				echo "<td style='text-align:right'>".$stock_value."</td>";
				echo "</tr>";
				$tot_sales_price+=$data['sales_price'];
				$tot_sales_price+=$data['sales_price'];
				$tot_stock_value+=$stock_value;
				$tot_stock+=$data['stock_qty'];

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold' colspan='2'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold'>".number_format($tot_sales_price,2)."</td>
					  <td style='text-align:right;font-weight:bold'></td>
					  <td style='text-align:right;font-weight:bold'>".number_format($tot_sales_price,2)."</td>
					  <td style='text-algin:right;font-weight:bold'>".number_format($tot_stock,2)."</td>
					  <td style='text-align:right;font-weight:bold'>".number_format($tot_stock_value,2)."</td>
				  </tr>";

        }else{
			echo "<tr>";
			echo "<td style='text-align:center;' colspan=7>No Records Found</td>";
			echo "</tr>";
		}
		
    }

	public function itemSales()
    {
		$this->loadModel("itemModel");
		$itemModel = $this->itemModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/itemsales",['itemData'=>$itemModel]);
    }

    public function showItemSalesReport()
    {
        $res = $this->reportModel->showItemSalesReport($_POST);
        if(!empty($res)){
			$i=0;
			$tot_total_cost=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td><a title='View Invoice' style='color:#f4f4f4' href='".PUBLIC_ROOT."sales/invoice/".$data['sales_id']."'>".$data['sales_id']."</a></td>";
				echo "<td >".$data['sales_date']."</td>";
				echo "<td >".$data['customer_name']."</td>";
				echo "<td>".$data['item_name']."</td>";
				echo "<td style='text-align:right'>".number_format($data['sales_qty'],2)."</td>";
				echo "<td style='text-align:right'>".number_format($data['total_cost'],2)."</td>";
				echo "</tr>";
				$tot_total_cost+=$data['total_cost'];

			}

			echo "<tr>
					  <td style='text-align:right;font-weight:bold;border:none !important;' colspan='6'><b>Total :</b></td>
					  <td style='text-align:right;font-weight:bold;border:none !important;'>".number_format($tot_total_cost,2)."</td>
				  </tr>";
		}
		else{
			echo "<tr>";
			echo "<td colspan='7' style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
		
    }
	public function expiredItems()
    {
		$this->loadModel("itemModel");
		$itemModel = $this->itemModel->getDataTable();
       return $this->view->renderWithLayouts(Config::get("VIEWS_PATH")."layout/" , Config::get("VIEWS_PATH"). "report/expireditemsreport",['itemData'=>$itemModel]);
    }

    public function showExpiredItemsReport()
    {
        $res = $this->reportModel->showExpiredItemsReport($_POST);
        if(!empty($res)){
			$i=0;
			foreach ($res as $data) {
				echo "<tr>";
				echo "<td>".++$i."</td>";
				echo "<td >".$data['item_id']."</td>";
				echo "<td>".$data['item_name']."</td>";
				echo "<td >".$data['expire_date']."</td>";
				echo "<td style='text-align:right'>".number_format($data['stock_qty'],2)."</td>";
				echo "</tr>";
			}
		}
		else{
			echo "<tr>";
			echo "<td colspan='5' style='text-align:center'>No Records Found</td>";
			echo "</tr>";
		}
		
    }
    public function isAuthorized()
    {
        $action = $this->request->param('action');
        $role = Session::getUserRole();
        $resource = "units";

        $action_alias = [
            'sales'=>'sales_report',
            'salesReturn'=>'sales_return_report',
        ];

        return Permission::check($role, $resource, $action, $action_alias);
    }
} 