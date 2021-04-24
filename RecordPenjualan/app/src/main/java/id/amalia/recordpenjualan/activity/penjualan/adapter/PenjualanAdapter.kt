package id.rdev.catatanpenjualan.activity.penjualan.adapter

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import id.rdev.catatanpenjualan.R
import id.rdev.catatanpenjualan.model.Penjualan
import id.rdev.catatanpenjualan.utils.Uang
import kotlinx.android.synthetic.main.item_keranjang.view.*

class PenjualanAdapter(val listBarang: List<Penjualan?>?, val onDelete: OnDelete) : RecyclerView.Adapter<PenjualanAdapter.MyHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyHolder {
        val v = LayoutInflater.from(parent.context).inflate(R.layout.item_keranjang, parent, false)

        return MyHolder(v)
    }

    override fun getItemCount(): Int = listBarang?.size ?: 0

    override fun onBindViewHolder(holder: MyHolder, position: Int) {
        holder.bind(listBarang?.get(position), position)

        val penjualan = listBarang?.get(position)
        holder.itemView.ibKeranjangDeleteItem.setOnClickListener {
            onDelete.click(penjualan)
        }
    }

    class MyHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {

        fun bind(penjualan: Penjualan?, position : Int) {
            itemView.tvKeranjangNo.text = "${position+1}"
            itemView.tvKeranjangName.text = penjualan?.namaBarang
            itemView.tvKeranjangQty.text = "${penjualan?.qty} x"
            itemView.tvKeranjangHargaJual.text = "${Uang.indonesia(penjualan?.hargaJual ?: 0.0)}"
            itemView.tvKeranjangTotalItem.text = "= ${Uang.indonesia(penjualan?.hargaJual?.times(penjualan?.qty ?: 1) ?: 0.0)}"
        }
    }

    interface OnDelete {
        fun click(penjualan: Penjualan?)
    }
}