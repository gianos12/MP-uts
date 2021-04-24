package id.rdev.catatanpenjualan.activity.login

import id.rdev.catatanpenjualan.model.User

interface LoginView {
    fun onSuccessLogin(user: User?)
    fun onErrorLogin(msg: String?)
}