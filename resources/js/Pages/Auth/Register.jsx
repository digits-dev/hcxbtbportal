import React, { useRef, useState } from "react";
import InputWithLogo from "../../Components/Forms/InputWithLogo";
import { Head, Link, router, useForm } from "@inertiajs/react";
import LoginInput from "../../Components/Forms/LoginInput";
import CheckboxWithText from "../../Components/Checkbox/CheckboxWithText";
import Modalv2 from "../../Components/Modal/Modalv2";

const Register = ({ email }) => {
    const { data, setData, reset, post, processing, errors } = useForm({
        first_name: "",
        last_name: "",
        email: email || "",
        password: "",
        confirm_password: "",
        profile_photo: null,   
    });

    const [successModal, setSuccessModal] = useState(false);
    const [frondEnderrors, setErrors] = useState({});
    const [passwordStrength, setPasswordStrength] = useState('');
    const [isUpperCase, setIsUpperCase] = useState(false);
    const [isLowerCase, setIsLowerCase] = useState(false);
    const [isCorrectLength, setIsCorrectLength] = useState(false);
    const [isSpecialChar, setIsSpecialChar] = useState(false);
    const [isNumber, setIsNumber] = useState(false);
    const [isChecked, setIsChecked] = useState(false);

    const handleCheckboxClick = () => {
        setIsChecked(!isChecked);
    };

    function handleChange(e) {
        const key = e.target.name;
        const value = e.target.value;
        setData((resetPasswordData) => ({
            ...resetPasswordData,
            [key]: value,
        }));
        setErrors((prevErrors) => ({ ...prevErrors, [key]: "" }));
    }

    const handlePasswordChange = (e) => {
        const newPassword = e.target.value;
        setData((prevData) => ({
            ...prevData,
            password: newPassword
        }));
    
        setPasswordStrength(checkPasswordStrength(newPassword));
    };
    

    const checkPasswordStrength = (password) => {
        let strength = 0;
    
        const newState = {
            isCorrectLength: password.length >= 8,
            isUpperCase: /[A-Z]/.test(password),
            isLowerCase: /[a-z]/.test(password),
            isNumber: /[0-9]/.test(password),
            isSpecialChar: /[^A-Za-z0-9]/.test(password),
        };
    
        // Calculate strength
        strength += newState.isCorrectLength ? 1 : 0;
        strength += newState.isUpperCase ? 1 : 0;
        strength += newState.isLowerCase ? 1 : 0;
        strength += newState.isNumber ? 1 : 0;
        strength += newState.isSpecialChar ? 1 : 0;
    
        // Update all states at once
        setIsCorrectLength(newState.isCorrectLength);
        setIsUpperCase(newState.isUpperCase);
        setIsLowerCase(newState.isLowerCase);
        setIsNumber(newState.isNumber);
        setIsSpecialChar(newState.isSpecialChar);
    
        return (strength / 5) * 100; // Return percentage value for the progress bar
    };
    

    const validate = () => {
        const newErrors = {};
        if (!data.first_name){
            newErrors.first_name = "First Name is required";
        }

        if (!data.last_name){
            newErrors.last_name = "Last Name is required";
        }
        
        if (!data.password){
            newErrors.password = "Password is required";
        }
        else {
            const password = data.password;
            // Validate password length (at least 8 characters)
            if (password.length < 8) {
                newErrors.password = "Password must be at least 8 characters long";
            }
            // Validate at least one uppercase letter
            if (!/[A-Z]/.test(password)) {
                newErrors.password = "Password must contain at least one uppercase letter";
            }
            // Validate at least one lowercase letter
            if (!/[a-z]/.test(password)) {
                newErrors.password = "Password must contain at least one lowercase letter";
            }
            // Validate at least one number
            if (!/[0-9]/.test(password)) {
                newErrors.password = "Password must contain at least one number";
            }
            // Validate at least one special character
            if (!/[@$!%*#?&]/.test(password)) {
                newErrors.password = "Password must contain at least one special character";
            }
        }
           
        if (!data.confirm_password)
            newErrors.confirm_password = "Confirm Password is required";
        if (data.password != data.confirm_password) {
            newErrors.confirm_password = "Passwords not Match";
        }

        return newErrors;
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const newErrors = validate();
        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
        } else {
            post('/register', {
                onSuccess: (data) => {
                    setSuccessModal(true);
                },
                onError: (data) => {
                }
            });
        }
    };

    const handleForgetKey = () => {
        post('/forget_session_key', {
            onSuccess: (data) => {
            },
            onError: (data) => {
            }
        });
    }

    // FOR IMAGE
    const [selectedImage, setSelectedImage] = useState(null);
    const fileInputRef = useRef(null);

    const handleImageChange = (event) => {
        if (event.target.files && event.target.files[0]) {
            const file = event.target.files[0];
            setSelectedImage(URL.createObjectURL(file));
            setData("profile_photo", file);
            setIsButtonDisable(false);
        }
    };

    return (
        <>
            <Head title='Reset Password'/>
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-black px-4 py-8">
                {/* CARD */}
                <div className="w-fit max-w-4xl relative backdrop-blur-sm bg-white border border-gray-200/50 shadow-2xl shadow-black/20 rounded-2xl overflow-hidden">
                    <div className="flex min-h-[600px] ">
                        <form onSubmit={handleSubmit} className="md:w-1/2 p-8 flex w-96 z-50 items-center justify-center flex-col m-5">
                            <p className='text-black text-xl md:text-2xl font-semibold mb-10'>Account Registration</p>
                            {/* PHOTO */}
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
                            {/* OTHER FORMS */}
                            <LoginInput
                                placeholder="Email"
                                isDisabled
                                addMainClass="mb-2"
                                value={data.email}
                                onError={errors.email}
                                type="text"
                            />
                            <div className="md:grid md:grid-cols-2 md:gap-1 space-y-2 md:space-y-0 w-full">
                                <LoginInput
                                    placeholder="First Name"
                                    name="first_name"
                                    type="text"
                                    value={data.first_name}
                                    onError={frondEnderrors.first_name || errors.first_name}
                                    onChange={(e) =>setData("first_name", e.target.value)}
                                />
                                <LoginInput
                                    placeholder="Last Name"
                                    name="last_name"
                                    value={data.last_name}
                                    type="text"
                                    onError={frondEnderrors.last_name || errors.last_name}
                                    onChange={(e) =>setData("last_name", e.target.value)}
                                />
                            </div>
                            <LoginInput
                                addMainClass="mt-2"
                                placeholder="Enter Password"
                                name="password"
                                value={data.password}
                                type={isChecked ? 'text' : 'password'}
                                onError={frondEnderrors.password || errors.password}
                                onChange={handlePasswordChange}
                            />
                            {data.password && (
                                <div className="mt-3 w-full">
                                    <div className="relative w-full h-3 bg-gray-200 rounded">
                                        <div
                                            className={`absolute top-0 left-0 h-full rounded transition-all ${passwordStrength < 40 ? 'bg-red-500': passwordStrength < 70 ? 'bg-yellow-400': 'bg-green-500'}`}
                                            style={{
                                                width: `${passwordStrength}%`
                                            }}
                                        ></div>
                                    </div>
                                    <div className="text-xs mt-1 text-white">
                                        {passwordStrength < 40
                                            ? 'Weak Password'
                                            : passwordStrength < 70
                                            ? 'Medium Password'
                                            : 'Strong Password'}
                                    </div>
                                    <div className="text-xs mt-1 text-gray-300">
                                        <div className={`${isUpperCase && 'text-green-500'}`}><i className={`${isUpperCase ? 'fa-solid fa-check' : 'fa-solid fa-circle-info text-xs'} mr-1`}></i><span>Atleast 1 Uppercase Letter</span></div>
                                        <div className={`${isLowerCase && 'text-green-500'}`}><i className={`${isLowerCase ? 'fa-solid fa-check' : 'fa-solid fa-circle-info text-xs'} mr-1`}></i><span>Atleast 1 Lowercase Letter</span></div>
                                        <div className={`${isCorrectLength && 'text-green-500'}`}><i className={`${isCorrectLength ? 'fa-solid fa-check' : 'fa-solid fa-circle-info text-xs'} mr-1`}></i><span>Atleast 8 Characters Long</span></div>
                                        <div className={`${isSpecialChar && 'text-green-500'}`}><i className={`${isSpecialChar ? 'fa-solid fa-check' : 'fa-solid fa-circle-info text-xs'} mr-1`}></i><span>Atleast 1 Special Character</span></div>
                                        <div className={`${isNumber && 'text-green-500'}`}><i className={`${isNumber ? 'fa-solid fa-check' : 'fa-solid fa-circle-info text-xs'} mr-1`}></i><span>Atleast 1 Number</span></div>
                                    </div>
                                </div>
                            )}
                            <LoginInput
                                addMainClass="mt-2"
                                placeholder="Confirm Password"
                                value={data.confirm_password}
                                name="confirm_password"
                                type={isChecked ? 'text' : 'password'}
                                onError={frondEnderrors.confirm_password || errors.confirm_password}
                                onChange={handleChange}
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
                                className={`w-full font-open-sans bg-gradient-to-br from-gray-900 via-gray-800 to-black  mt-4 text-white p-2 text-xs cursor-pointer rounded-lg text-center font-medium hover:opacity-70 md:text-base md:p-2.5 disabled:cursor-not-allowed `}
                                type="submit"
                                disabled={processing}
                                >
                                    {processing ? 'Please Wait' : 'Create Account'}
                            </button>
                            <p className='text-xs md:text-sm mt-5 text-black '><span>Already have account?</span> <Link href='/login' className='text-gray-500 font-semibold hover:opacity-70'>Back to Login</Link></p>
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
            {/* MODAL */}
            {successModal && 
                <div
                    className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="modal-title"
                >
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all duration-300 scale-100 opacity-100">
                        {/* Modal Content */}
                        <div className="p-10 text-center">
                            {/* Success Icon */}
                            <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                                <svg className="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            {/* Title */}
                            <h2 id="modal-title" className="text-lg font-semibold text-gray-900 mb-2">
                                Registration Successful!
                            </h2>

                            {/* Description */}
                            <p className="text-gray-600 text-sm mb-6">
                            Your account has been created successfully. Please wait for a follow-up email with your activation notification 
                            </p>

                            {/* Action Button */}
                            <button
                                onClick={handleForgetKey}
                                className="bg-black text-sm hover:bg-gray-800 text-white font-medium py-2 px-6 rounded-md transition-colors duration-200 w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                            >
                                Go to Login
                            </button>
                        </div>
                    </div>
                </div>
            }
        </>
    );
};

export default Register;
