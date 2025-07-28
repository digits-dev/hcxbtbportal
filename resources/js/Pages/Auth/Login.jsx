import React, { useState, useEffect, useRef } from 'react';
import { usePage, router, Link, Head, useForm } from '@inertiajs/react';
import { useAuth } from '../../Context/AuthContext';
import LoginInput from '../../Components/Forms/LoginInput';
import CheckboxWithText from '../../Components/Checkbox/CheckboxWithText';


const LoginPage = () => {
    const { updateAuth } = useAuth();
    const [formType, setFormType] = useState('Sign In');
    const [isChecked, setIsChecked] = useState(false);
    const { data, setData, processing, reset, post, errors } = useForm({
        email: "",
        password: "",
        register_email: "",
    });


    const handleCheckboxClick = () => {
        setIsChecked(!isChecked);
    };

    const handleChangeFormClick  = ($formType) => {
        setFormType($formType);
        setData({
            email: "",
            password: "",
            register_email: "",
        });
    }

    const handleLogin = (e) => {
        e.preventDefault();
         post('post_login', {
            onSuccess: (page) => {
                const newAuthState = page.props.auth;
                updateAuth(newAuthState);

                reset();
            },
            onError: (error) => {
            }
        });
        
    }

    const handleEmailVerification = (e) => {
        e.preventDefault();
        post('email_verification', {
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
            onError: (error) => {
            }
        });
    }


    return (
        <>
            <Head title='Login'/>
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-black px-4 py-8">
                {/* CARD */}
                <div className="w-fit md:w-full max-w-4xl relative backdrop-blur-sm bg-white border border-gray-200/50 shadow-2xl shadow-black/20 rounded-2xl overflow-hidden">
                    <div className="flex min-h-[600px]">
                        {formType == 'Sign In' ? 
                            <form onSubmit={handleLogin} className="w-80 md:w-1/2 p-8 flex z-50 items-center justify-center flex-col m-5">
                                <p className='text-black text-2xl md:text-3xl font-semibold'>Login</p>
                                <LoginInput
                                    addMainClass="mt-1"
                                    placeholder="Enter Email"
                                    title="Email"
                                    name="email"
                                    type="text"
                                    onError={errors.email}
                                    onChange={(e) =>setData("email", e.target.value)
                                    }
                                />
                                <LoginInput
                                    addMainClass="mt-1"
                                    placeholder="Enter Password"
                                    title="Password"
                                    name="password"
                                    type={isChecked ? 'text' : 'password'}
                                    onError={errors.password}
                                    onChange={(e) =>setData("password", e.target.value)
                                    }
                                />
                                <CheckboxWithText
                                    id="custom-checkbox"         
                                    type="checkbox"             
                                    name="exampleCheckbox"      
                                    handleClick={handleCheckboxClick} 
                                    isChecked={isChecked}        
                                    disabled={false}  
                                    addMainClass="justify-end mt-2"         
                                />
                                <button 
                                    className={`w-full font-open-sans bg-gradient-to-br from-gray-900 via-gray-800 to-black mt-4 text-white p-2 text-xs cursor-pointer rounded-lg text-center font-medium hover:opacity-70 md:text-base md:p-2.5 disabled:cursor-not-allowed `}
                                    type="submit"
                                    disabled={processing}
                                    >
                                        {processing ? 'Please Wait' : 'Login'}
                                </button>
                                <p className='text-xs md:text-sm mt-5 text-black mb-5'><span>Forgot the password ?</span> <Link href='reset_password' className='text-gray-500 font-semibold hover:opacity-70'>Click here</Link></p>
                                <div className='md:hidden flex items-center justify-center flex-col'>
                                    <p className="text-xs md:text-sm mb-1">
                                        Are you a Home Credit Staff?
                                    </p>
                                    <p className="text-xs md:text-sm">
                                        Click <span onClick={()=>handleChangeFormClick('Sign Up')} className='text-red-500 font-semibold'>Here</span> to register
                                    </p>
                                </div>
                            </form>
                            :
                            <form onSubmit={handleEmailVerification} className="w-full md:w-1/2 flex z-50 justify-center flex-col m-5">
                                    <p className='text-black text-2xl md:text-2xl font-semibold mb-4 text-center'>Sign Up</p>
                                    <p className='text-center mb-2 font-semibold'>Email Verification Required</p>
                                    <p className='text-[12px] mb-1'>To continue, please enter your email address. We'll send you a verification link to confirm your identity and keep your account secure.</p>
                                    <p className='text-[12px] mb-5'>Once you receive the email, click the verification link to complete the process.</p>
                                    <LoginInput
                                        addMainClass="mt-1"
                                        placeholder="Email Address"
                                        name="email"
                                        type="text"
                                        onError={errors.register_email}
                                        onChange={(e) =>setData("register_email", e.target.value)}
                                    />
                                <button 
                                    className={`w-full mb-5 font-open-sans bg-gradient-to-br from-gray-900 via-gray-800 to-black mt-4 text-white p-2 text-xs cursor-pointer rounded-lg text-center font-medium hover:opacity-70 md:text-base md:p-2.5 disabled:cursor-not-allowed `}
                                    type="submit"
                                    disabled={processing}
                                    >
                                        {processing ? 'Please Wait' : 'Send Verification Link'}
                                </button>
                                <div className='md:hidden flex items-center justify-center flex-col'>
                                    <p className="text-xs md:text-sm mb-1">
                                        Already have an account?
                                    </p>
                                    <p className="text-xs md:text-sm">
                                        Click <span onClick={()=>handleChangeFormClick('Sign In')} className='text-red-500 font-semibold'>Here</span> to Login
                                    </p>
                                </div>
                            </form>
                        }
                        
                        <div className="w-1/2 bg-gradient-to-br from-gray-800 to-black text-black p-12 hidden md:flex flex-col justify-center items-center text-center">
                            <div>
                                <div className='flex items-center space-x-4 justify-center mb-5 '>
                                    <img className='w-auto h-12 md:w-auto md:h-14 mb-3 z-50' src='/images/login-page/home-credit-logo.png'/>
                                    <div className='h-20 border-l-2'/>
                                    <img className='w-12 h-12 md:w-16 md:h-16 mb-3 z-50' src='/images/login-page/btb-logo.png'/>
                                </div>
                                <p className="text-gray-300 mb-16 leading-relaxed">
                                    Home Credit | BTB Portal
                                </p>
                                <p className="text-sm text-gray-400 mb-1">
                                    {formType == 'Sign In' ? 'Are you a Home Credit Staff?' : 'Already have an account?'}
                                </p>
                                <p className="text-sm text-gray-400">
                                    {formType == 'Sign In' ? 'Click Sign Up button to register' : 'Click Sign In to Login'}
                                    
                                </p>
                                {formType == 'Sign In' ?
                                    <button 
                                        className={`px-5 py-2 w-fit font-open-sans bg-gradient-to-br from-gray-300 via-gray-400 to-white mt-4 text-white text-xs cursor-pointer rounded-full text-center font-medium hover:opacity-70 md:text-base disabled:cursor-not-allowed `}
                                        disabled={processing}
                                        onClick={()=>handleChangeFormClick('Sign Up')}
                                    >
                                            Sign Up
                                    </button>
                                    :
                                    <button 
                                        className={`px-5 py-2 w-fit font-open-sans bg-gradient-to-br from-gray-300 via-gray-400 to-white mt-4 text-white text-xs cursor-pointer rounded-full text-center font-medium hover:opacity-70 md:text-base disabled:cursor-not-allowed `}
                                        disabled={processing}
                                        onClick={()=>handleChangeFormClick('Sign In')}
                                    >
                                            Sign In
                                    </button>
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
        
    );
};

export default LoginPage;
