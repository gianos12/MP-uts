package id.rdev.catatanpenjualan.activity.data_barang.adapter

import android.view.LayoutInflater
import android.view.MenuItem
import android.view.View
import android.view.ViewGroup
import android.widget.PopupMenu
import androidx.recyclerview.widget.RecyclerView
import id.rdev.catatanpenjualan.R
import id.rdev.catatanpenjualan.model.Barang
import kotlinx.android.synthetic.main.item_data_barang.view.*

class DataBarangAdapter(val barang: List<Barang?>?, val onMenuClicked: OnMenuClicked) : RecyclerView.Adapter<DataBarangAdapter.MyHolder>() {
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyHolder {
        val v = LayoutInflater.from(parent.context).inflate(R.layout.item_data_barang, parent, false)
        return MyHolder(v)
    }

    override fun getItemCount(): Int = barang?.size ?: 0

    override fun onBindViewHolder(holder: MyHolder, position: Int) {
        holder.bind(barang?.get(position))

        holder.itemView.ivMenuBarang.setOnClickListener {
            val popupMenu = PopupMenu(holder.itemView.context, it)
            popupMenu.menuInflater.inflate(R.menu.menu_barang, popupMenu.menu)
            popupMenu.show()

            popupMenu.setOnMenuItemClickListener {
                onMenuClicked.click(it, barang?.get(position))
                true
            }
        }
    }

    class MyHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        fun bind(barang: Barang?) {
            itemView.tvBarcode.text = barang?.barcode
            itemView.tvNamaBarang.text = barang?.namaBarang
            itemView.tvCategory.text = barang?.kategori
        }
    }

    interface OnMenuClicked {
        fun click(menuItem: MenuItem, barang: Barang?)
    }
}