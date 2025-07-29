import React from 'react'
import Button from '../../Components/Table/Buttons/Button';
import { useTheme } from '../../Context/ThemeContext';
import { useToast } from '../../Context/ToastContext';
import useThemeStyles from '../../Hooks/useThemeStyles';
import InputComponent from '../../Components/Forms/Input';
import { router, useForm, usePage } from '@inertiajs/react';
import DropdownSelect from '../../Components/Dropdown/Dropdown';

const StatusesAction = ({action, onClose, updateData, all_active_brand_groups, all_brand_groups}) => {
    const { theme } = useTheme();
    const { handleToast } = useToast();
    const { auth } = usePage().props;
    const privilege  = auth.sessions.admin_privileges;

    const { primayActiveColor, textColorActive, buttonSwalColor } = useThemeStyles(theme);

    const { data, setData, processing, reset, post, errors } = useForm({
        id: "" || updateData.id,
        name: "" || updateData.name,
        color: "#000000" || updateData.color,
        status: "" || updateData.status,
    });

    const statuses = [
        {
            id: 'ACTIVE',
            name:'ACTIVE',
        },
        {
            id: 'INACTIVE',
            name:'INACTIVE',
        },
    ]

    const predefinedColors = [
        "#ef4444", // red
        "#f97316", // orange
        "#eab308", // yellow
        "#22c55e", // green
        "#06b6d4", // cyan
        "#3b82f6", // blue
        "#8b5cf6", // violet
        "#ec4899", // pink
        "#64748b", // slate
        "#dc2626", // red-600
        "#ea580c", // orange-600
        "#ca8a04", // yellow-600
        "#16a34a", // green-600
        "#0891b2", // cyan-600
        "#2563eb", // blue-600
        "#7c3aed", // violet-600
        "#db2777", // pink-600
        "#475569", // slate-600
    ]

    const handleColorSelect = (color) => {
        setData((prev) => ({ ...prev, color }))
    }


    const handleFormSubmit = (e) => {
        e.preventDefault();
        Swal.fire({
            title: `<p class="font-poppins text-3xl" >Do you want ${action == 'Add' ? 'add' : 'update'} Status?</p>`,
            showCancelButton: true,
            confirmButtonText: `${action == 'Add' ? 'Confirm' : 'Update'}`,
            confirmButtonColor: buttonSwalColor,
            icon: 'question',
            iconColor: buttonSwalColor,
            reverseButtons: true,
        }).then(async (result) => {
            if (result.isConfirmed) {

                if (action == 'Add'){
                    post('statuses/create', {
                        onSuccess: (data) => {
                            const { message, type } = data.props.auth.sessions;
                            handleToast(message, type);
                            router.reload({ only: ["statuses"] });
                            reset();
                            onClose();
                        },
                        onError: (error) => {
                        }
                    });
                }
                else{
                    post('statuses/update', {
                        onSuccess: (data) => {
                            const { message, type } = data.props.auth.sessions;
                            handleToast(message, type);
                            router.reload({ only: ["statuses"] });
                            reset();
                            onClose();
                        },
                        onError: (error) => {
                        }
                    });
                }
                
            }
        });
    }

  return (
    <form onSubmit={handleFormSubmit} className='space-y-3'>
        {/* STATUS NAME */}
        <InputComponent
            name="name"
            displayName="Status Name"
            value={data.name}
            disabled={action === 'View'}
            placeholder="Enter Status Name"
            onError={errors.name}
            onChange={(e)=> setData("name", e.target.value.toUpperCase())}
        />
        {/* COLOR */}
        <div>
            <label
                className={`block text-xs font-bold ${theme === 'bg-skin-black' ? ' text-gray-400' : 'text-gray-700'}  font-poppins`}
            >
                Color
            </label>
            <div className="flex items-center gap-2">
                <div
                    className="w-8 h-8 rounded border-2 mt-1 border-gray-300 flex-shrink-0"
                    style={{ backgroundColor: data.color }}
                />
                <InputComponent
                    addClass="w-full" 
                    value={data.color}
                    disabled={action === 'View'}
                    placeholder="Add Color eg. #000000"
                    onError={errors.color}
                    onChange={(e)=> setData("color", e.target.value)}
                />
            </div>
        </div>
        {action !== 'View' && 
            <div className="mt-5">
                <p className={`block text-xs font-bold ${theme === 'bg-skin-black' ? ' text-gray-400' : 'text-gray-700'}  font-poppins`}>Quick Colors</p>
                <div className="grid grid-cols-9 gap-2 mt-2">
                    {predefinedColors.map((color) => (
                    <button
                        key={color}
                        type="button"
                        className={`w-8 h-8 rounded border-2 hover:scale-110 transition-transform ${
                        data.color === color ? "border-gray-900" : "border-gray-300"
                        }`}
                        style={{ backgroundColor: color }}
                        onClick={() => handleColorSelect(color)}
                        title={color}
                    />
                    ))}
                </div>
            </div>
        }
        {action == 'Update' && 
            <>
                <DropdownSelect
                    placeholder="Choose Status"
                    selectType="react-select"
                    defaultSelect="Select Status"
                    onChange={(selectedOption) => setData("status", selectedOption?.value)}
                    name="status"
                    menuPlacement="top"
                    options={statuses}
                    value={data.status ? { label: data.status, value: data.status } : null}
                />
                {(errors.status) && (
                    <div className="font-poppins text-xs font-semibold text-red-600">
                        {errors.status}
                    </div>
                )}
            </>
        }

        {action == "View" && 
            <div className='flex items-center space-x-2'>
                <div className={`block text-sm font-bold ${theme === 'bg-skin-black' ? ' text-gray-400' : 'text-gray-700'}  font-poppins`}>
                    Status
                </div>
                <div className={`select-none ${data.status == 'ACTIVE' ? 'bg-status-success': 'bg-status-error'} mb-2 text-sm font-poppins font-semibold py-1 px-3 text-center text-white rounded-full mt-2`}>
                    {data.status}
                </div>
            </div>
        }
        
        
        {action !== 'View' && 
            <div className='flex justify-end'>
                <Button
                    type="button"
                    extendClass={`${theme === 'bg-skin-white' ? primayActiveColor : theme} mt-3`}
                    fontColor={textColorActive}
                    disabled={processing}
                >
                {processing ? 
                    (
                        action === "Add" ? 'Submitting' : 'Updating'
                    ) 
                    : 
                    (
                        <span>
                            <i className={`fa-solid ${action === "Add" ? 'fa-plus' : 'fa-pen-to-square' } mr-1`}></i> {action === "Add" ? 'Add Status' : 'Update Status'}
                        </span>
                    )
                }
                </Button>  
            </div>
        }
        
    </form>
  )
}

export default StatusesAction