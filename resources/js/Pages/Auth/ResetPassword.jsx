import React, { useState, useEffect } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import getAppName from '../../Components/SystemSettings/ApplicationName';
import getAppLogo from '../../Components/SystemSettings/ApplicationLogo';
import LoginDetails from '../../Components/SystemSettings/LoginDetails';
import LoginInput from '../../Components/Forms/LoginInput';
const ResetPassword = () => {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/send_resetpass_email', {
            onSuccess: () => {
                reset();
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Email sent, Please check your email',
                }).then(() => {
                    router.visit('/login');
                });
            },
        });
    };

    return (
        <>
            <Head title='Forgot Password'/>
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-black px-4 py-8">
                {/* CARD */}
                <div className="w-fit md:w-full max-w-4xl relative backdrop-blur-sm bg-white border border-gray-200/50 shadow-2xl shadow-black/20 rounded-2xl overflow-hidden">
                    <div className="flex min-h-[600px]">
                        <form onSubmit={handleSubmit} className="md:w-1/2 p-8 flex z-50 items-center justify-center flex-col m-5">
                            <p className='text-black text-xl md:text-2xl font-semibold mb-10'>Forgot Password</p>
                            <p className="text-red-300 text-xs md:text-sm w-full">
                                *will send instructions by your email
                            </p>
                            <LoginInput
                                addMainClass="mt-2"
                                placeholder="Enter your Email"
                                title="Email"
                                name="email"
                                type="text"
                                onError={errors.email}
                                onChange={(e) =>setData("email", e.target.value)
                                }
                            />
                            <button 
                                className={`w-full font-open-sans bg-gradient-to-br from-gray-900 via-gray-800 to-black mt-4 text-white p-2 text-xs cursor-pointer rounded-lg text-center font-medium hover:opacity-70 md:text-base md:p-2.5 disabled:cursor-not-allowed `}
                                type="submit"
                                disabled={processing}
                                >
                                    {processing ? 'Please Wait' : 'Send Email'}
                            </button>
                            <p className='text-xs md:text-sm mt-5 text-black '><span>Already know the password?</span> <Link href='login' className='text-gray-500 font-semibold hover:opacity-70'>Back to Login</Link></p>
                        </form>          
                        <div className="w-1/2 bg-gradient-to-br hidden from-gray-800 to-black text-black p-12 md:flex flex-col justify-center items-center text-center">
                            <div>
                                <div className='flex items-center space-x-4 justify-center mb-5 '>
                                    <img className='w-auto h-12 md:w-auto md:h-14 mb-3 z-50' src='/images/login-page/home-credit-logo.png'/>
                                    <div className='h-20 border-l-2'/>
                                    <img className='w-12 h-12 md:w-16 md:h-16 mb-3 z-50' src='/images/login-page/btb-logo.png'/>
                                </div>
                                <p className="text-gray-300 mb-16 leading-relaxed">
                                    Home Credit | BTB Portal
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ResetPassword;
