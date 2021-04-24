package id.rdev.catatanpenjualan.activity.report.detail.presenter

import id.rdev.catatanpenjualan.activity.report.detail.data.ResultItemKeranjang
import id.rdev.catatanpenjualan.network.NetworkConfig
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class DetailReportPresenter {

    fun getPenjualan(id_user: Int, id_keranjang: Int, onResult: OnResult){
        NetworkConfig.service()
            .getItemKeranjang(id_user,  id_keranjang)
            .enqueue(object: Callback<ResultItemKeranjang> {

                override fun onFailure(call: Call<ResultItemKeranjang>, t: Throwable) {
                    onResult.onFailed(t.localizedMessage)
                }

                override fun onResponse(
                    call: Call<ResultItemKeranjang>,
                    response: Response<ResultItemKeranjang>
                ) {
                    val body = response?.body()
                    if (body?.status == 200) {
                        onResult.onResult(body)
                    } else {
                        onResult.onFailed(body?.message)
                    }
                }
            })
    }

    interface OnResult{
        fun onResult(resultItemKeranjang: ResultItemKeranjang)
        fun onFailed(msg : String?)
    }
}