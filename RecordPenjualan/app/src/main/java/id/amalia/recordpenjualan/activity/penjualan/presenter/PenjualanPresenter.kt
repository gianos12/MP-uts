package id.rdev.catatanpenjualan.activity.penjualan.presenter

import androidx.annotation.IntegerRes
import id.rdev.catatanpenjualan.activity.penjualan.data.ResultKeranjang
import id.rdev.catatanpenjualan.activity.penjualan.data.ResultSearchBarang
import id.rdev.catatanpenjualan.model.Barang
import id.rdev.catatanpenjualan.model.Keranjang
import id.rdev.catatanpenjualan.model.Penjualan
import id.rdev.catatanpenjualan.network.NetworkConfig
import id.rdev.catatanpenjualan.response.ResultSimple
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class PenjualanPresenter(val penjualanView: PenjualanView) {

    fun getKeranjang(status : String, id_user: Int?) {
        NetworkConfig.service()
            .getKeranjang(status, id_user)
            .enqueue(object: Callback<ResultKeranjang>{
                override fun onFailure(call: Call<ResultKeranjang>, t: Throwable) {
                    penjualanView.onFailedGetKeranjang(t.localizedMessage)
                }

                override fun onResponse(
                    call: Call<ResultKeranjang>,
                    response: Response<ResultKeranjang>
                ) {
                    val body = response.body()
                    if (body?.status == 200) {
                        penjualanView.onSuccessGetKeranjang(body?.keranjang)
                    } else {
                        penjualanView.onFailedGetKeranjang(body?.message)
                    }
                }
            })
    }

    fun searchBarang(keyword: String, id_user: Int?) {
        NetworkConfig.service()
            .searchBarang(keyword, id_user)
            .enqueue(object: Callback<ResultSearchBarang>{
                override fun onFailure(call: Call<ResultSearchBarang>, t: Throwable) {
                    penjualanView.onFailedSearchItem(t.localizedMessage)
                }

                override fun onResponse(
                    call: Call<ResultSearchBarang>,
                    response: Response<ResultSearchBarang>
                ) {
                    val body = response.body()
                    if (body?.status == 200) {
                        penjualanView.onSuccessSearchItem(body?.barang)
                    } else {
                        penjualanView.onFailedSearchItem(body?.message)
                    }
                }
            })
    }


    fun addItemToKeranjang(qty: Int, barang: Barang?, keranjang: Keranjang?) {
        NetworkConfig.service()
            .addItemToKeranjang(Integer.parseInt(keranjang?.idUser), Integer.parseInt(keranjang?.idKeranjang), Integer.parseInt(barang?.idBarang),
                barang?.namaBarang, qty, barang?.hargaBeli, barang?.hargaJual)
            .enqueue(object: Callback<ResultSimple>{
                override fun onFailure(call: Call<ResultSimple>, t: Throwable) {
                    penjualanView.onFailedAddItemToKeranjang(t.localizedMessage)
                }

                override fun onResponse(
                    call: Call<ResultSimple>,
                    response: Response<ResultSimple>
                ) {
                    val body = response.body()
                    if (body?.status == 200) {
                        penjualanView.onSuccessAddItemToKeranjang(body?.message)
                    } else {
                        penjualanView.onFailedAddItemToKeranjang(body?.message)
                    }
                }
            })
    }

    fun deleteItemKeranjang(penjualan: Penjualan?) {
        NetworkConfig.service()
            .deleteItemKeranjang(Integer.parseInt(penjualan?.idUser),
                Integer.parseInt(penjualan?.idKeranjang),
                Integer.parseInt(penjualan?.idBarang),
                Integer.parseInt(penjualan?.idPenjualan))
            .enqueue(object: Callback<ResultSimple>{
                override fun onFailure(call: Call<ResultSimple>, t: Throwable) {
                    penjualanView.onFailedDeleteItemKeranjang(t.localizedMessage)
                }

                override fun onResponse(
                    call: Call<ResultSimple>,
                    response: Response<ResultSimple>
                ) {
                    val body = response.body()
                    if (body?.status == 200) {
                        penjualanView.onSuccessDeleteItemKeranjang(body?.message)
                    } else {
                        penjualanView.onFailedDeleteItemKeranjang(body?.message)
                    }
                }

            })
    }

    fun jualBarang(id_user : Int?, id_keranjang : Int?,status : String?,qty : Int?,total_harga : Double?) {
        NetworkConfig.service()
            .jualBarang(id_user, id_keranjang, status, qty, total_harga)
            .enqueue(object: Callback<ResultSimple>{
                override fun onFailure(call: Call<ResultSimple>, t: Throwable) {
                    penjualanView.onFailedJualBarang(t.localizedMessage)

                }

                override fun onResponse(
                    call: Call<ResultSimple>,
                    response: Response<ResultSimple>
                ) {
                    val body = response.body()
                    if (body?.status == 200) {
                        penjualanView.onSuccessJualBarang(body?.message)

                    } else {
                        penjualanView.onFailedJualBarang(body?.message)
                    }
                }

            })
    }

    fun addKeranjang(id_user: Int?) {
        NetworkConfig.service()
            .addKeranjang(id_user, "PENDING")
            .enqueue(object: Callback<ResultSimple>{
                override fun onFailure(call: Call<ResultSimple>, t: Throwable) {
                    penjualanView.onFailedAddKeranjang(t.localizedMessage)
                }

                override fun onResponse(
                    call: Call<ResultSimple>,
                    response: Response<ResultSimple>
                ) {
                    val body = response.body()
                    if (body?.status == 200) {
                        penjualanView.onSuccessAddKeranjang(body?.message)
                    } else {
                        penjualanView.onFailedAddKeranjang(body?.message)
                    }
                }

            })
    }

}