package id.rdev.catatanpenjualan.model

import javax.annotation.Generated
import com.google.gson.annotations.SerializedName

@Generated("com.robohorse.robopojogenerator")
data class Penjualan(

	@field:SerializedName("id_penjualan")
	val idPenjualan: String? = null,

	@field:SerializedName("id_keranjang")
	val idKeranjang: String? = null,

	@field:SerializedName("id_barang")
	val idBarang: String? = null,

	@field:SerializedName("harga_beli")
	val hargaBeli: Double? = null,

	@field:SerializedName("qty")
	val qty: Int? = null,

	@field:SerializedName("nama_barang")
	val namaBarang: String? = null,

	@field:SerializedName("id_user")
	val idUser: String? = null,

	@field:SerializedName("created_date")
	val createdDate: String? = null,

	@field:SerializedName("harga_jual")
	val hargaJual: Double? = null
)