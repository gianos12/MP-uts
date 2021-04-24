package id.rdev.catatanpenjualan.model

import javax.annotation.Generated
import com.google.gson.annotations.SerializedName
import java.io.Serializable

@Generated("com.robohorse.robopojogenerator")
data class User(

	@field:SerializedName("password")
	var password: String? = null,

	@field:SerializedName("hp")
	var hp: String? = null,

	@field:SerializedName("id_user")
	var idUser: Int? = null,

	@field:SerializedName("create_date")
	var createDate: String? = null,

	@field:SerializedName("email")
	var email: String? = null,

	@field:SerializedName("username")
	var username: String? = null
) : Serializable