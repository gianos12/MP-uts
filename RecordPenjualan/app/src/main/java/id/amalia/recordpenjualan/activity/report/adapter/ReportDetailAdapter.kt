package id.rdev.catatanpenjualan.activity.report.detail.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import id.rdev.catatanpenjualan.R

import id.rdev.catatanpenjualan.activity.report.detail.data.ResultItemKeranjang

import kotlinx.android.synthetic.main.item_keranjang.view.*

class ReportDetailAdapter(
    private val mValues: ResultItemKeranjang
) : RecyclerView.Adapter<ReportDetailAdapter.ViewHolder>() {


    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_keranjang, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(mValues, position)
    }

    override fun getItemCount(): Int = mValues.keranjang?.penjualan?.size ?: 0

    inner class ViewHolder(val mView: View) : RecyclerView.ViewHolder(mView) {
        fun bind(keranjang: ResultItemKeranjang, position: Int) {
            itemView.tvKeranjangNo.text = "${position+1}"
            itemView.tvKeranjangName.text = keranjang.keranjang?.penjualan?.get(position)?.namaBarang
            itemView.tvKeranjangQty.text = "${keranjang.keranjang?.penjualan?.get(position)?.qty} x"
            itemView.tvKeranjangHargaJual.text = "${keranjang.keranjang?.penjualan?.get(position)?.hargaJual}"
            itemView.ibKeranjangDeleteItem.visibility = View.GONE
            itemView.tvKeranjangTotalItem.text = "= ${keranjang.keranjang?.penjualan?.get(position)?.hargaJual?.times(keranjang.keranjang?.penjualan?.get(position)?.qty ?: 1)}"
        }
    }
}
