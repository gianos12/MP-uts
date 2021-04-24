package id.rdev.catatanpenjualan.base

import android.annotation.SuppressLint
import android.app.Activity
import androidx.appcompat.app.AppCompatActivity
import id.rdev.catatanpenjualan.model.User
import org.jetbrains.anko.toast



@SuppressLint("Registered")
open class BaseActivity : AppCompatActivity() {
    object TAGS {
        val BARANG = "barang"
        val USER = "user"
    }

    var user : User? = null

    fun cekSesi(activity: Activity) {
        val intent = intent.getSerializableExtra("user")

        if (intent == null) {
            activity.toast("Anda belum login").show()
            activity.finish()
        } else {
            user = intent as User
        }
    }
}