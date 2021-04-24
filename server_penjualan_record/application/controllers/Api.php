<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    function __construct(Type $foo = null)
    {
        parent::__construct();

        date_default_timezone_set('Asia/Jakarta');
        error_reporting(E_ALL);
        ini_set('displays_errors', 1);
    }

    /***
    * API halaman Penjualan
    */
    function addKeranjang($id_user = '', $status = 'PENDING')
    {
	$encodeJson = ($id_user == '') ? true : false;
        // buat baru
        $newKeranjang['id_user'] = $id_user;
        $newKeranjang['status'] = $status;

        $q = $this->db->insert('keranjang', $newKeranjang);

        if ($q) {
            $data['message'] = 'success';
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }
	if ($encodeJson)
            echo json_encode($data);
        return $q;
    }

    function getKeranjang($id_user = '', $status = '')
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('id_user', $id_user);
        $this->db->where('status', $status);

        $q = $this->db->get('keranjang');

        if ($q->num_rows() == 0) {

            if ($this->addKeranjang($id_user)) {
                // berhasil nambah keranjang baru
                $this->getKeranjang($id_user, $status);
                return;
            } else {
                $data['message'] = 'error';
                $data['status'] = 404;
            }
        } else {
            $data['message'] = 'success';
            $data['status'] = 200;

            // get penjualan by id keranjang
            $keranjang = $q->result();

            for($i = 0; $i < count($keranjang); $i++) {
                $this->db->where('id_user', $id_user);
                $this->db->where('id_keranjang', $keranjang[$i]->id_keranjang);
                $q = $this->db->get('penjualan');
                $keranjang[$i]->penjualan = $q->result();
            }
            $data['keranjang'] = $keranjang;
        }

        echo json_encode($data);
    }

    function addItemToKeranjang()
    {
        $simpan['id_user'] = $this->input->post('id_user');
        $simpan['id_keranjang'] = $this->input->post('id_keranjang');
        $simpan['id_barang'] = $this->input->post('id_barang');
        $simpan['nama_barang'] = $this->input->post('nama_barang');
        $simpan['qty'] = $this->input->post('qty');
        $simpan['harga_beli'] = $this->input->post('harga_beli');
        $simpan['harga_jual'] = $this->input->post('harga_jual');

        // ganti qty dan harga di keranjang


        $qty = $simpan['qty'];
        $total_harga = $qty * $simpan['harga_jual'];

        $this->db->set('qty', "qty + ".(int)$qty, false);
        $this->db->set('total_harga', "total_harga + ".(int)$total_harga, false);

        $this->db->where('id_user', $simpan['id_user']);
        $this->db->where('id_keranjang', $simpan['id_keranjang']);

        $this->db->update('keranjang');

        // >


        $q = $this->db->insert('penjualan', $simpan);

        if ($q) {
            $data['message'] = 'berhasil menambah item';
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }
        echo json_encode($data);
    }

    function deleteItemKeranjang()
    {
        $simpan['id_penjualan'] = $this->input->post('id_penjualan');
        $simpan['id_user'] = $this->input->post('id_user');
        $simpan['id_keranjang'] = $this->input->post('id_keranjang');
        $simpan['id_barang'] = $this->input->post('id_barang');

        $this->db->where('id_penjualan', $simpan['id_penjualan']);
        $this->db->where('id_user', $simpan['id_user']);
        $this->db->where('id_keranjang', $simpan['id_keranjang']);
        $this->db->where('id_barang', $simpan['id_barang']);

        $q = $this->db->delete('penjualan');

        if ($q) {
            $data['message'] = 'berhasil menghapus';
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }
        echo json_encode($data);

    }

    function searchBarang() {
        $keyword = $this->input->post('keyword');
        $id_user = $this->input->post('id_user');

        $this->db->where('id_user', $id_user);
        $this->db->like('nama_barang', $keyword);
        $this->db->or_like('barcode', $keyword);

        $q = $this->db->get('barang');

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['barang'] = $q->result();
        } else {
            $data['message'] = 'tidak ditemukan';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function jualBarang() {
        $id_keranjang = $this->input->post('id_keranjang');
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');
        $qty = $this->input->post('qty');
        $total_harga = $this->input->post('total_harga');

        $this->db->where('id_user', $id_user);
        $this->db->where('id_keranjang', $id_keranjang);

        $update['qty'] = $qty;
        $update['total_harga'] = $total_harga;
        $update['status'] = $status;
        $update['date'] = date("Y-m-d H:i:s");

        $q = $this->db->update('keranjang', $update);

        if ($q) {
            $data['message'] = 'success';
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function getReport()
    {
        $id_user = $this->input->post('id_user');

        $this->db->where('id_user', $id_user);
        $this->db->order_by('status', 'asc');
        $this->db->order_by('date', 'desc');

        $q = $this->db->get('keranjang');

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function getItemKeranjang()
    {
        $id_user = $this->input->post('id_user');
        $id_keranjang = $this->input->post('id_keranjang');

        $this->db->where('id_keranjang', $id_keranjang);
        $this->db->where('id_user', $id_user);

        $ker = $this->db->get('keranjang');

        if ($ker->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $ker->row();

            $this->db->where('id_keranjang', $id_keranjang);
            $this->db->where('id_user', $id_user);
            $q = $this->db->get('penjualan');

            if ($q->num_rows() > 0) {
                $data['keranjang']->penjualan = $q->result();
            }
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }
        echo json_encode($data);
    }

    function getLastDay()
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('status', $status);
        $this->db->where('id_user', $id_user);
        $now = date("Y-m-d");

        // get yesterday data
        $now = date("Y-m-d");
        $yesterday_start =  date("Y-m-d", strtotime('-1 day')) . " 00:00:00";
        $yesterday_last = date("Y-m-d", strtotime('-1 day')) . " 23:59:59";


        $query = "SELECT * FROM keranjang WHERE id_user=$id_user AND status='TERJUAL' AND date >= '$yesterday_start' AND date <= '$yesterday_last'";
        // echo $query;

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }
    function getNowDay()
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('status', $status);
        $this->db->where('id_user', $id_user);
        $now = date("Y-m-d");

        // get yesterday data
        $now = date("Y-m-d");
        $yesterday_start =  date("Y-m-d") . " 00:00:00";
        $yesterday_last = date("Y-m-d") . " 23:59:59";


        $query = "SELECT * FROM keranjang WHERE id_user=$id_user AND status='TERJUAL' AND date >= '$yesterday_start' AND date <= '$yesterday_last'";
        // echo $query;

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function getLastWeek()
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('status', $status);
        $this->db->where('id_user', $id_user);
        $now = date("Y-m-d");

        // get last week
        $yesterday_start =  date("Y-m-d", strtotime('monday this week - 7 day')) . " 00:00:00";
        $yesterday_last = date("Y-m-d", strtotime('monday this week - 1 day')) . " 23:59:59";


        $query = "SELECT * FROM keranjang WHERE id_user=$id_user AND status='TERJUAL' AND date >= '$yesterday_start' AND date <= '$yesterday_last'";
        // echo $query;

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }
    function getNowWeek()
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('status', $status);
        $this->db->where('id_user', $id_user);
        $now = date("Y-m-d");

        // get last week
        $yesterday_start =  date("Y-m-d", strtotime('monday this week')) . " 00:00:00";
        $yesterday_last = date("Y-m-d", strtotime('monday this week + 7 day')) . " 23:59:59";


        $query = "SELECT * FROM keranjang WHERE id_user=$id_user AND status='TERJUAL' AND date >= '$yesterday_start' AND date <= '$yesterday_last'";
        // echo $query;

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function getLastMonth()
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('status', $status);
        $this->db->where('id_user', $id_user);
        $now = date("Y-m-d");

        // get last week
        $yesterday_start =  date("Y-m-01", strtotime('-1 month')) . " 00:00:00";
        $yesterday_last = date("Y-m-d", strtotime('last day of -1 month')) . " 23:59:59";

        $query = "SELECT * FROM keranjang WHERE id_user=$id_user AND status='TERJUAL' AND date >= '$yesterday_start' AND date <= '$yesterday_last'";
        // echo $query;

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }
    function getNowMonth()
    {
        $id_user = $this->input->post('id_user');
        $status = $this->input->post('status');

        $this->db->where('status', $status);
        $this->db->where('id_user', $id_user);
        $now = date("Y-m-d");

        // get last week
        $yesterday_start =  date("Y-m-01") . " 00:00:00";
        $yesterday_last = date("Y-m-d", strtotime('last day of 0 month')) . " 23:59:59";

        $query = "SELECT * FROM keranjang WHERE id_user=$id_user AND status='TERJUAL' AND date >= '$yesterday_start' AND date <= '$yesterday_last'";
        // echo $query;

        $q = $this->db->query($query);

        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['keranjang'] = $q->result();
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }


    /**
     * Login Register API
     *
     */

    function registerUser()
    {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $hp = $this->input->post('hp');

        $this->db->where('username', $username);
        $this->db->or_where('email', $email);
        $this->db->or_where('hp', $hp);


        $q = $this->db->get('users');

        if ($q->num_rows() > 0) {
            $data['message'] = 'email atau username atau hp sudah terdaftar, silahkan sign in';
            $data['status'] = 404;
        } else {
            $simpan['username'] = $username;
            $simpan['email'] = $email;
            $simpan['password'] = password_hash($password, PASSWORD_DEFAULT);
            $simpan['hp'] = $hp;

            $q = $this->db->insert('users', $simpan);

            if ($q) {
                $data['message'] = 'berhasil registrasi '.$username;
                $data['status'] = 200;
            } else {
                $data['message'] = 'error';
                $data['status'] = 404;
            }
        }
        echo json_encode($data);
    }

    function loginUser()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $this->db->where('username', $username);
        $this->db->where('password', password_verify($password, PASSWORD_DEFAULT));

        $q = $this->db->get('users');
        if ($q->num_rows() > 0) {
            $data['message'] = 'success';
            $data['status'] = 200;
            $data['user'] = $q->row();
        } else {
            $data['message'] = 'username atau password salah';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    /**
     * API DATA BARANG CRUD
     *
     */
    function getDataBarang()
    {
        $id_user = $this->input->post('id_user');

        $this->db->where('id_user', $id_user);
	$this->db->order_by('nama_barang', 'asc');
        $q = $this->db->get('barang');

        if ($q->num_rows() > 0) {
            $data['barang'] = $q->result();
            $data['message'] = 'success';
            $data['status'] = 200;
        } else {
            $data['message'] = 'Tidak ada data barang';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function addBarang()
    {
        $nama_barang = $this->input->post('nama_barang');

        $simpan['id_user'] = $this->input->post('id_user');
        $simpan['barcode'] = $this->input->post('barcode');
        $simpan['nama_barang'] = $nama_barang;
        $simpan['kategori'] = $this->input->post('kategori');
        $simpan['harga_beli'] = $this->input->post('harga_beli');
        $simpan['harga_jual'] = $this->input->post('harga_jual');

        $q = $this->db->insert('barang', $simpan);

        if ($q) {
            $data['message'] = 'berhasil menambah '.$nama_barang;
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function updateBarang()
    {
        $id_barang = $this->input->post('id_barang');
        $id_user = $this->input->post('id_user');
        $nama_barang = $this->input->post('nama_barang');

        $simpan['barcode'] = $this->input->post('barcode');
        $simpan['nama_barang'] = $nama_barang;
        $simpan['kategori'] = $this->input->post('kategori');
        $simpan['harga_beli'] = $this->input->post('harga_beli');
        $simpan['harga_jual'] = $this->input->post('harga_jual');

        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_user', $id_user);


        $q = $this->db->update('barang', $simpan);

        if ($q) {
            $data['message'] = 'berhasil update '.$nama_barang;
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    function deleteBarang()
    {
        $id_barang = $this->input->post('id_barang');
        $id_user = $this->input->post('id_user');
        $nama_barang = $this->input->post('nama_barang');

        $this->db->where('id_barang', $id_barang);
        $this->db->where('id_user', $id_user);


        $q = $this->db->delete('barang');

        if ($q) {
            $data['message'] = 'berhasil hapus '.$nama_barang;
            $data['status'] = 200;
        } else {
            $data['message'] = 'error';
            $data['status'] = 404;
        }

        echo json_encode($data);
    }

    //
    //
    // function loginGmail(){
    //
	//     $gmail = $this->input->post('email');
	//     $name = $this->input->post('name');
    //
    //
    //
	//     $this->db->where('user_email',$gmail);
    //
	//     $q = $this->db->get('tb_user');
    //
	//     if($q -> num_rows() > 0){
	//         $data['data'] = $q ->row();
	//         $data['message'] = 'login gmail';
	//         $data['status'] = 200 ;
	//     }
	//     else{
    //
	//        $simpan['user_email'] = $gmail ;
	//        $simpan['user_nama'] = $name ;
	//        $simpan['user_status'] = 2 ;
	//        $simpan['user_level'] = 1 ;
	//        $simpan['user_tanggal'] = date('Y-m-d H:i:s');
	//        $q  =$this->db->insert('tb_user',$simpan);
    //
	//        if($q){
	//            $data['message'] = 'register';
	//            $data['status'] = 200 ;
    //            $data['user_id'] = $this->db->insert_id();
	//        }
	//        else{
	//              $data['message'] = 'error';
	//            $data['status'] = 404 ;
	//        }
	//     }
    //
	//     echo json_encode($data);
	// }
    //
    // function updateHpUser()
    // {
    //     $idUser = $this->input->post('idUser');
    //     $hp = $this->input->post('hp');
    //
    //     $this->db->where('user_id', $idUser);
    //
    //     $simpan['user_hp'] = $hp;
    //
    //     $q = $this->db->update('tb_user', $simpan);
    //
    //     if($q){
    //         $data['message'] = 'success';
    //         $data['status'] = 200 ;
    //     }
    //     else{
    //         $data['message'] = 'error';
    //         $data['status'] = 404 ;
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function getProduk()
    // {
    //     $q = $this->db->get('tb_produk');
    //
    //     if ($q->num_rows() > 0) {
    //         $data['message'] = 'success';
    //         $data['status'] = 200;
    //         $data['data'] = $q->result();
    //     } else {
    //         $data['message'] = 'error';
    //         $data['status'] = 404;
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function getKategori()
    // {
    //     $q = $this->db->get('tb_kategori');
    //
    //     if ($q->num_rows() > 0) {
    //         $data['message'] = 'success';
    //         $data['status'] = 200;
    //         $data['kategori'] = $q->result();
    //     } else {
    //         $data['message'] = 'error';
    //         $data['status'] = 404;
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function promosi()
    // {
    //     $this->db->where('is_promote', true);
    //
    //     $q = $this->db->get('tb_produk');
    //
    //     if ($q->num_rows() > 0) {
    //         $data['message'] = 'success';
    //         $data['status'] = 200;
    //         $data['data'] = $q->result();
    //     } else {
    //         $data['message'] = 'error';
    //         $data['status'] = 404;
    //     }
    //
    //     echo json_encode($data);
    // }
    // function popular()
    // {
    //     $this->db->where_in('produk_rating', array(4,5));
    //
    //     $q = $this->db->get('tb_produk');
    //
    //     if ($q->num_rows() > 0) {
    //         $data['message'] = 'success';
    //         $data['status'] = 200;
    //         $data['data'] = $q->result();
    //     } else {
    //         $data['message'] = 'error';
    //         $data['status'] = 404;
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function produkPerKategori()
    // {
    //     $this->db->where('produk_kategori', $this->input->post('id'));
    //
    //     $q = $this->db->get('tb_produk');
    //
    //     if ($q->num_rows() > 0) {
    //         $data['message'] = 'success';
    //         $data['status'] = 200;
    //         $data['data'] = $q->result();
    //     } else {
    //         $data['message'] = 'error';
    //         $data['status'] = 404;
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    //
    // function order()
    // {
    //     $iduser = $this->input->post('iduser');
    //     $total = $this->input->post('total');
    //     $idproduk = $this->input->post('idproduk');
    //     $qty = $this->input->post('qty');
    //     $harga = $this->input->post('harga');
    //
    //     // simpan tb_order
    //
    //     $simpan['order_user'] = $iduser;
    //     $simpan['order_total'] = $total;
    //
    //     $q = $this->db->insert('tb_order', $simpan);
    //
    //     $idorder = $this->db->insert_id();
    //
    //     // simpan tb_detailOrder
    //     if ($q) {
    //         $data['idOrder'] = $idorder;
    //         $data['status'] = 200;
    //         $data['message'] = 'success';
    //
    //         $save['detail_order'] = $idorder;
    //         $save['detail_produk'] = $idproduk;
    //         $save['detail_qty'] = $qty;
    //         $save['detail_harga'] = $harga;
    //
    //         $query = $this->db->insert('tb_detailOrder', $save);
    //     } else {
    //         $data['status'] = 200;
    //         $data['message'] = 'error';
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function addItemKeranjang()
    // {
    //     $idorder = $this->input->post('idorder');
    //     $idproduk = $this->input->post('idproduk');
    //     $qty = $this->input->post('qty');
    //     $harga = $this->input->post('harga');
    //
    //     $save['detail_order'] = $idorder;
    //     $save['detail_produk'] = $idproduk;
    //     $save['detail_qty'] = $qty;
    //     $save['detail_harga'] = $harga;
    //
    //     $q = $this->db->insert('tb_detailOrder', $save);
    //
    //     if ($q) {
    //         $data['status'] = 200;
    //         $data['message'] = 'success';
    //     } else {
    //         $data['status'] = 404;
    //         $data['message'] = 'error';
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // public function getKeranjang() {
    //     $idorder = $this->input->post('idOrder');
    //
    //     $this->db->join('tb_produk', 'tb_produk.produk_id = tb_detailOrder.detail_produk');
    //
    //     $q = $this->db->where('detail_order', $idorder);
    //
    //     $q = $this->db->get('tb_detailOrder');
    //
    //     if ($q -> num_rows() > 0) {
    //         $data['keranjang'] = $q -> result();
    //         $data['status'] = 200;
    //         $data['msg'] = "success";
    //     } else {
    //         $data['status'] = 404;
    //         $data['msg'] = "error";
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function changeStatus()
    // {
    //     $idorder = $this->input->post('idOrder');
    //     $toStatus = $this->input->post('toStatus');
    //
    //     $this->db->where('order_id', $idorder);
    //
    //     $update['order_status'] = $toStatus;
    //
    //     $q = $this->db->update('tb_order',$update);
    //
    //     if($q){
    //         $data['message'] = 'success';
    //         $data['status'] = 200 ;
    //     }
    //     else{
    //         $data['message'] = 'error';
    //         $data['status'] = 404 ;
    //     }
    //
    //     echo json_encode($data);
    // }
    //
    // function getHistory()
    // {
    //     $iduser = $this->input->post('idUser');
    //     $this->db->where('order_user', $iduser);
    //
    //     $q = $this->db->get('tb_order');
    //
    //     if ($q -> num_rows() > 0) {
    //         $data['data'] = $q -> result();
    //         $data['status'] = 200;
    //         $data['msg'] = "success";
    //     } else {
    //         $data['status'] = 404;
    //         $data['msg'] = "error";
    //     }
    //
    //     echo json_encode($data);
    // }
}
