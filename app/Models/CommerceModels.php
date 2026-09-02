<?php
namespace App\Models;
use CodeIgniter\Model;
abstract class CommerceModel extends Model { protected $returnType='array'; protected $useTimestamps=true; }
class AdminModel extends CommerceModel { protected $table='admins'; protected $allowedFields=['name','email','password']; }
class UserModel extends CommerceModel { protected $table='users'; protected $allowedFields=['email','name','phone']; }
class OtpLoginModel extends CommerceModel { protected $table='otp_logins'; protected $useTimestamps=false; protected $allowedFields=['email','otp_code','expires_at','is_used','created_at']; }
class CategoryModel extends CommerceModel { protected $table='categories'; protected $allowedFields=['name','slug']; }
class ProductModel extends CommerceModel { protected $table='products'; protected $allowedFields=['category_id','name','slug','description','price','sale_price','image','hover_image','stock','status']; }
class ProductImageModel extends CommerceModel
{
    protected $table = 'product_images';
    protected $allowedFields = ['product_id', 'image', 'sort_order'];

    /** Remove stale database rows from storefront output when the file is gone. */
    public static function onlyExisting(array $images): array
    {
        return array_values(array_filter($images, static function (array $image): bool {
            $filename = basename((string) ($image['image'] ?? ''));

            return $filename !== '' && is_file(FCPATH . 'uploads/products/' . $filename);
        }));
    }
}
class ProductReviewModel extends CommerceModel { protected $table='product_reviews'; protected $allowedFields=['product_id','user_id','rating','review','status','verified_purchase']; }
class SliderModel extends CommerceModel { protected $table='sliders'; protected $allowedFields=['eyebrow','title','description','button_text','button_url','image','sort_order','status']; }
class AddressModel extends CommerceModel { protected $table='addresses'; protected $useTimestamps=false; protected $allowedFields=['user_id','full_name','phone','line1','line2','city','state','pincode','is_default','created_at']; }
class CartItemModel extends CommerceModel { protected $table='cart_items'; protected $allowedFields=['user_id','session_id','product_id','quantity']; }
class OrderModel extends CommerceModel { protected $table='orders'; protected $allowedFields=['user_id','address_id','total_amount','status','payment_status','payment_method','gateway_order_id']; }
class OrderItemModel extends CommerceModel { protected $table='order_items'; protected $useTimestamps=false; protected $allowedFields=['order_id','product_id','product_name','quantity','price_at_purchase']; }
class PaymentModel extends CommerceModel { protected $table='payments'; protected $useTimestamps=false; protected $allowedFields=['order_id','gateway','transaction_id','amount','status','raw_response','notes','created_at']; }
