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
        profile_photo: null,    
    });

    const [selectedImage, setSelectedImage] = useState(null);
    const fileInputRef = useRef(null);

    const handleCheckboxClick = () => {
        setIsChecked(!isChecked);
    };

    const handleImageChange = (event) => {
        if (event.target.files && event.target.files[0]) {
            const file = event.target.files[0];
            setSelectedImage(URL.createObjectURL(file));
            setData("profile_photo", file);
            setPhotoName(file.name);
            setIsButtonDisable(false);
        }
    };

    const handleRemoveImage = () => {
        setSelectedImage(null);
        setData("profile_photo", null);
        setPhotoName(null);
        setIsButtonDisable(true);

        if (fileInputRef.current) {
            fileInputRef.current.value = null;
        }
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

    const handleAccountCreate = (e) => {
        e.preventDefault();
        post('create_account', {
            onSuccess: (page) => {
                const newAuthState = page.props.auth;
                updateAuth(newAuthState);

                reset();
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
                <div className="w-full max-w-4xl relative backdrop-blur-sm bg-white border border-gray-200/50 shadow-2xl shadow-black/20 rounded-2xl overflow-hidden">
                    <div className="flex min-h-[600px]">
                        {formType == 'Sign In' ? 
                            <form onSubmit={handleLogin} className="w-1/2 p-8 flex z-50 items-center justify-center flex-col m-5">
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
                                <p className='text-xs md:text-sm mt-5 text-black '><span>Forgot the password ?</span> <Link href='reset_password' className='text-gray-500 font-semibold hover:opacity-70'>Click here</Link></p>
                            </form>
                            :
                            <form className="w-1/2 flex z-50 items-center justify-center flex-col m-5">
                                <p className='text-black text-2xl md:text-2xl font-semibold mb-5'>Sign Up</p>
                                <div className='w-20 h-20 md:w-36 md:h-36 border-2 md:border-4 rounded-full  overflow-hidden  border-accent2 items-center justify-center relative mb-5'>
                                    {selectedImage ? (
                                        <>
                                            <img
                                                className="w-full h-full absolute object-contain"
                                                id="image"
                                                src={selectedImage}
                                                alt="Selected"
                                            />
                                            <div className='h-full w-full absolute items-center justify-center bg-black/50 flex'>
                                                <div className='absolute z-0 font-semibold border-dashed border-white text-white text-xs md:text-xs  border md:border-2 rounded-lg p-2'>
                                                    CHANGE PHOTO
                                                </div>
                                            </div>
                                        </>
                                    ) : (
                                        <>  
                                            <img
                                                className="w-full h-full absolute object-fit"
                                                id="image"
                                                src="/Images/Others/user-icon.png"
                                                alt="Upload"
                                            />
                                            <div className='h-full w-full absolute items-center justify-center bg-black/50 flex'>
                                                <div className='absolute z-0 font-semibold border-dashed border-white text-white text-xs md:text-xs  border md:border-2 rounded-lg p-2'>
                                                    UPLOAD PHOTO
                                                </div>
                                            </div>
                                            
                                        </>
                                    )}     
                                    <input
                                        required
                                        id="input-file"
                                        name="image"
                                        type="file"
                                        ref={fileInputRef}
                                        accept="image/*"
                                        className="w-full z-20 h-full rounded-full opacity-0 cursor-pointer"
                                        onChange={handleImageChange}
                                    />
                                </div>
                                <div className="md:grid md:grid-cols-2 md:gap-1 space-y-2 md:space-y-0">
                                    <LoginInput
                                        placeholder="First Name"
                                        name="first_name"
                                        type="text"
                                        onError={errors.email}
                                        onChange={(e) =>setData("email", e.target.value)}
                                    />
                                    <LoginInput
                                        placeholder="Last Name"
                                        name="last_name"
                                        type="text"
                                        onError={errors.email}
                                        onChange={(e) =>setData("email", e.target.value)}
                                    />
                                   
                                </div>
                                <LoginInput
                                    addMainClass="mt-1"
                                    placeholder="Email Address"
                                    name="email"
                                    type="text"
                                    onError={errors.email}
                                    onChange={(e) =>setData("email", e.target.value)}
                                />
                                <LoginInput
                                    addMainClass="mt-1"
                                    placeholder="Enter Password"
                                    name="register_password"
                                    type={isChecked ? 'text' : 'password'}
                                    onError={errors.register_password}
                                    onChange={(e) =>
                                        setData("register_password", e.target.value)
                                    }
                                />
                                <LoginInput
                                    addMainClass="mt-1"
                                    placeholder="Confirm Password"
                                    name="register_confirm_password"
                                    onError={errors.register_confirm_password}
                                    type={isChecked ? 'text' : 'password'}
                                    onChange={(e) =>
                                        setData("register_confirm_password", e.target.value)
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
                                        {processing ? 'Please Wait' : 'Create Account'}
                                </button>
                            </form>
                        }
                        
                        <div className="w-1/2 bg-gradient-to-br from-gray-800 to-black text-black p-12 flex flex-col justify-center items-center text-center">
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
                                        onClick={()=>setFormType('Sign Up')}
                                    >
                                            Sign Up
                                    </button>
                                    :
                                    <button 
                                        className={`px-5 py-2 w-fit font-open-sans bg-gradient-to-br from-gray-300 via-gray-400 to-white mt-4 text-white text-xs cursor-pointer rounded-full text-center font-medium hover:opacity-70 md:text-base disabled:cursor-not-allowed `}
                                        disabled={processing}
                                        onClick={()=>setFormType('Sign In')}
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
