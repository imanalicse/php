<?php
namespace App\SendgridQuickstart;

use App\Logger\Log;
use App\MySQL\QueryBuilder;
use App\SendgridQuickstart\Enum\EmailStatusNumeric;

class EmailFunctions
{
    public function isSentOrderEmail($order_id) : int {
        try {
            $query_builder = new QueryBuilder();
            $order = $query_builder->get("orders")->where(["id" => $order_id])->find();
            if (!empty($order) && $order['email_status'] == EmailStatusNumeric::IS_SENT) {
                return EmailStatusNumeric::IS_SENT;
            }
        } catch (\Exception $exception) {
            Log::write('Error: isSentOrderEmail for order:' . $exception->getMessage(), 'email_tracker_error');
        }
        return 0;
    }

    public function updateOrderEmailStatus($order_id, $email_status)
    {
        try {
            $query_builder = new QueryBuilder();
            $order = $query_builder->get("orders")->where(["id" => $order_id])->find();
            if ($order['email_status'] != EmailStatusNumeric::IS_SENT) {
                (new QueryBuilder())->update("orders", ["email_status" => $email_status], ["id" => $order_id]);
            }
        } catch (\Exception $exception) {

        }
    }
}
