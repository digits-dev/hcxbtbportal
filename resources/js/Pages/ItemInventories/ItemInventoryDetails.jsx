import { Head } from '@inertiajs/react'
import React from 'react'
import ContentPanel from '../../Components/Table/ContentPanel'
import InputComponent from '../../Components/Forms/Input'
import { Calendar, Hash, Package, ShoppingBasket, StretchHorizontal, Tag, User } from 'lucide-react'
import Button from '../../Components/Table/Buttons/Button'
import { useTheme } from '../../Context/ThemeContext'
import useThemeStyles from '../../Hooks/useThemeStyles'

const ItemInventoriesDetails = ({page_title, inventory_details}) => {

    const { theme } = useTheme();
    const { textColorActive } = useThemeStyles(theme);

    console.log(inventory_details);

    const ItemOrderCard = ({order}) => {
        return (
            <div className='border rounded-md flex items-center gap-4 p-4'>
                <div className="flex items-center justify-center w-10 h-10 bg-slate-800 rounded-lg">
                    <ShoppingBasket className="h-5 w-5 text-white"/>
                </div>
                <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-3 mb-2">
                        <p className="text-base font-semibold text-gray-900 truncate">{order?.get_order?.reference_number}</p>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div className="space-y-1">
                            <div className="flex items-center gap-2 text-gray-600">
                                <User className="h-3 w-3" />
                                <span>Customer Name: {order?.get_order?.first_name} {order?.get_order?.last_name}</span>
                            </div>
                            <div className="flex items-center gap-2 text-gray-600">
                                <Tag className="h-3 w-3" />
                                <span>Status: {order?.get_order?.get_status?.name}</span>
                            </div>
                        </div>

                        <div className="space-y-1">
                            <div className="flex items-center gap-2 text-gray-600">
                                <Package className="h-3 w-3" />
                                <span>Qty: {order?.quantity ?? 0} units</span>
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
            <p className='mb-5 font-bold text-md'>Inventory Details</p>
            <div className="md:grid md:grid-cols-2 md:gap-1 space-y-2 md:space-y-0 w-full">
                <InputComponent
                    displayName="Digits Code"
                    value={inventory_details?.digits_code}
                    onChange={()=>{}}
                    disabled={true}
                />
                <InputComponent
                    displayName="Item Description"
                    value={inventory_details?.get_item?.item_description}
                    onChange={()=>{}}
                    disabled={true}
                />
                <InputComponent
                    displayName="Stock"
                    value={inventory_details?.qty}
                    onChange={()=>{}}
                    disabled={true}
                />
                <InputComponent
                    displayName="Ordered Quantity"
                    value={inventory_details?.reserved_qty}
                    onChange={()=>{}}
                    disabled={true}
                />
            </div>

            {inventory_details?.get_reserve_item?.length != 0 &&
                <>
                    <p className='mb-2 font-semibold text-sm mt-5'>Reserved Orders</p>
                    <div className='space-y-2'>
                    
                        {inventory_details?.get_reserve_item?.map((item, index)=>(
                            <ItemOrderCard key={index} order={item}/>
                        ))
                    }
                    </div>
                </>
            }

            <Button
                type="link"
                href="/item_inventories"
                extendClass={`${theme} w-fit mt-5`}
                fontColor={textColorActive}
            >
                    <span>Back</span>
            </Button>
            
        </ContentPanel>
    </>
  )
}

export default ItemInventoriesDetails