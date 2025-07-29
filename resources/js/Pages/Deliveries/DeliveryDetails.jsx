import { Head } from '@inertiajs/react'
import React from 'react'
import ContentPanel from '../../Components/Table/ContentPanel'
import InputComponent from '../../Components/Forms/Input'
import { Calendar, Hash, Package, StretchHorizontal, Tag } from 'lucide-react'
import Button from '../../Components/Table/Buttons/Button'
import { useTheme } from '../../Context/ThemeContext'
import useThemeStyles from '../../Hooks/useThemeStyles'

const DeliveryDetails = ({page_title, delivery_details}) => {

    const { theme } = useTheme();
    const { textColorActive } = useThemeStyles(theme);

    const ItemDeliveryCard = ({name, item_code, color, qty, received_at}) => {
        return (
            <div className='border rounded-md flex items-center gap-4 p-4'>
                <div className="flex items-center justify-center w-10 h-10 bg-slate-800 rounded-lg">
                    <StretchHorizontal className="h-5 w-5 text-white"/>
                </div>
                <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-3 mb-2">
                        <p className="text-base font-semibold text-gray-900 truncate">{name}</p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div className="space-y-1">
                            <div className="flex items-center gap-2 text-gray-600">
                                <Hash className="h-3 w-3" />
                                <span>Digits Code: {item_code}</span>
                            </div>
                            <div className="flex items-center gap-2 text-gray-600">
                                <Tag className="h-3 w-3" />
                                <span>Color: {color}</span>
                            </div>
                        </div>

                        <div className="space-y-1">
                            <div className="flex items-center gap-2 text-gray-600">
                                <Package className="h-3 w-3" />
                                <span>Qty: {qty ?? 0} units</span>
                            </div>
                            <div className="flex items-center gap-2 text-gray-600">
                                <Calendar className="h-3 w-3" />
                                <span>Received: {received_at}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
  return (
    <>
        <Head title={page_title}/>
        <ContentPanel>
            <p className='mb-5 font-bold text-md'>Delivery Details</p>
            <div className="md:grid md:grid-cols-2 md:gap-1 space-y-2 md:space-y-0 w-full">
                <InputComponent
                    displayName="Reference Number"
                    value={delivery_details?.reference_number}
                    onChange={()=>{}}
                    disabled={true}
                />
                <InputComponent
                    displayName="Received Date"
                    value={delivery_details?.received_at}
                    onChange={()=>{}}
                    disabled={true}
                />
            </div>

            <p className='mb-2 font-semibold text-sm mt-5'>Item Details</p>
            <div className='space-y-2'>
                {delivery_details?.get_lines.map((item, index)=>(
                    <ItemDeliveryCard 
                        key={index} 
                        name={item?.get_item?.item_description} 
                        item_code={item?.get_item?.digits_code}
                        color={item?.get_item?.actual_color}
                        qty={item?.qty}
                        received_at={item?.received_at}
                    />
                ))}
            </div>

            <Button
                type="link"
                href="/deliveries"
                extendClass={`${theme} w-fit mt-5`}
                fontColor={textColorActive}
            >
                    <span>Back</span>
            </Button>
            
        </ContentPanel>
    </>
  )
}

export default DeliveryDetails