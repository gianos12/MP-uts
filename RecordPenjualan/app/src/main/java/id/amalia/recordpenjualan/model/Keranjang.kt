package id.rdev.catatanpenjualan.model

import javax.annotation.Generated
import com.google.gson.annotations.SerializedName
import java.io.Serializable

enum class KeranjangStatus(val status : String){
	PENDING("PENDING"),
	TERJUAL("TERJUAL"),
	TERHAPUS("TERHAPUS")
}

@Generated("com.robohorse.robopojogenerator")
data class Keranjang(

	@field:SerializedName("date")
	val date: String? = null,

	@field:SerializedName("id_keranjang")
	val idKeranjang: String? = null,

	@field:SerializedName("qty")
	val qty: String? = null,

	@field:SerializedName("penjualan")
	val penjualan: List<Penjualan?>? = null,

	@field:SerializedName("total_harga")
	val totalHarga: Double? = null,

	@field:SerializedName("id_user")
	val idUser: String? = null,

	@field:SerializedName("status")
	val status: String? = null
) : Serializable