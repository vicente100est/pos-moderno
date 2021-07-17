<?php 
ob_start();
session_start();
include ("../_init.php");

// Check, if user logged in or not
// If user is not logged in then return an alert message
if (!is_loggedin()) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_login')));
  exit();
}

// Check, if user has reading permission or not
// If user have not reading permission return an alert message
if (user_group_id() != 1 && !has_permission('access', 'read_supplier_due_paid_report')) {
  header('HTTP/1.1 422 Unprocessable Entity');
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(array('errorMsg' => trans('error_read_permission')));
  exit();
}

$store_id = store_id();

// Fetch invoice 
if ($request->server['REQUEST_METHOD'] == 'GET' && isset($request->get['invoice_id']))
{
    try {

        if (empty($request->get['invoice_id'])) {
            throw new Exception(trans('error_invoice_id'));
        }

        $invoice_id = $request->get['invoice_id'];

        // Fetch invoice info
        $statement = db()->prepare("SELECT purchase_payments.* FROM `purchase_payments` 
            LEFT JOIN `purchase_price` ON (`purchase_payments`.`invoice_id` = `purchase_price`.`invoice_id`) 
            WHERE `purchase_payments`.`invoice_id` = ? AND `purchase_payments`.`store_id` = ?");
        $statement->execute(array($invoice_id, $store_id));
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);
        if (empty($invoice)) {
            throw new Exception(trans('error_purchase_payments_not_found'));
        }
        
        // Fetch invoice item
        $statement = db()->prepare("SELECT * FROM `purchase_item` WHERE invoice_id = ?");
        $statement->execute(array($invoice_id));
        $purchase_items = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($purchase_items)) {
            throw new Exception(trans('error_purchase_item'));
        }

        $invoice['items'] = $purchase_items;

        header('Content-Type: application/json');
        echo json_encode(array('msg' => trans('text_success'), 'invoice' => $invoice));
        exit();

    }
    catch(Exception $e) { 

        header('HTTP/1.1 422 Unprocessable Entity');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array('errorMsg' => $e->getMessage()));
        exit();
    }
}

/**
 *===================
 * START DATATABLE
 *===================
 */

$where_query = 'purchase_payments.store_id = ' . $store_id . ' AND purchase_payments.type = "due_paid"';

$from = from();
$to = to();
$where_query .= date_range_purchase_payments_filter($from, $to);

// DB table to use
$table = "(SELECT purchase_payments.* FROM purchase_payments 
  LEFT JOIN purchase_price ON (purchase_payments.invoice_id = purchase_price.invoice_id) 
  WHERE $where_query) as customers";

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array( 'db' => 'created_at', 'dt' => 'created_at' ),
    array( 'db' => 'invoice_id', 'dt' => 'invoice_id' ),
    array(
        'db'        => 'pmethod_id',
        'dt'        => 'pmethod_name',
        'formatter' => function($d, $row) {
            return get_the_pmethod($row['pmethod_id'], 'name');
        }
    ),
    array(
        'db'        => 'created_by',
        'dt'        => 'created_by',
        'formatter' => function($d, $row) {
            return get_the_user($row['created_by'], 'username');
        }
    ),
    array(
        'db'        => 'amount',
        'dt'        => 'amount',
        'formatter' => function($d, $row) {
            return currency_format($row['amount']);
        }
    ),
    array(
        'db'        => 'id',
        'dt'        => 'btn_view',
        'formatter' => function($d, $row) {
            return '<a class="btn btn-sm btn-block btn-info" href="#"><i class="fa fa-eye"></i></a>';
        }
    ),
);

echo json_encode(
    SSP::simple($request->get, $sql_details, $table, $primaryKey, $columns)
);

/**
 *===================
 * END DATATABLE
 *===================
 */