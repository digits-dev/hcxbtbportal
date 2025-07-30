import { Head, Link, router, usePage } from "@inertiajs/react";
import React, { useEffect, useState } from "react";
import ContentPanel from "../../Components/Table/ContentPanel";
import Tooltip from "../../Components/Tooltip/Tooltip";
import Button from "../../Components/Table/Buttons/Button";
import TopPanel from "../../Components/Table/TopPanel";
import Filters from "../../Components/Filters/Filters";
import Export from "../../Components/Table/Buttons/Export";
import { useTheme } from "../../Context/ThemeContext";
import useThemeStyles from "../../Hooks/useThemeStyles";
import TableSearch from "../../Components/Table/TableSearch";
import TableContainer from "../../Components/Table/TableContainer";
import Thead from "../../Components/Table/Thead";
import Row from "../../Components/Table/Row";
import TableHeader from "../../Components/Table/TableHeader";
import Tbody from "../../Components/Table/Tbody";
import RowStatus from "../../Components/Table/RowStatus";
import Pagination from "../../Components/Table/Pagination";
import RowData from "../../Components/Table/RowData";
import RowAction from "../../Components/Table/RowAction";
import CustomFilter from "../../Components/Table/Buttons/CustomFilter";
import moment from "moment/moment";

const Orders = ({page_title, queryParams, orders}) => {

    const { auth } = usePage().props;
    const { theme } = useTheme();
    const { primayActiveColor, textColorActive } = useThemeStyles(theme);
    const [pathname, setPathname] = useState(null);

    useEffect(() => {
        const segments = window.location.pathname.split("/");
        setPathname(segments.pop());
    }, []);

    const refreshTable = (e) => {
        e.preventDefault();
        router.get(pathname);
    };

    return (
        <>
            <Head title={page_title}/>
            <ContentPanel>
                <TopPanel>
                    <div className="inline-flex flex-wrap gap-1">
                        <Tooltip text="Refresh data" arrow="bottom">
                            <Button
                                extendClass={
                                    (["bg-skin-white"].includes(theme)
                                        ? primayActiveColor
                                        : theme) + " py-[5px] px-[10px]"
                                }
                                fontColor={textColorActive}
                                onClick={refreshTable}
                            >
                                <i className="fa fa-rotate-right text-base p-[1px]"></i>
                            </Button>
                        </Tooltip>
                        {auth.access.isCreate &&
                            <Button
                                extendClass={
                                    (["bg-skin-white"].includes(theme)
                                        ? primayActiveColor
                                        : theme) + " py-[5px] px-[10px]"
                                }
                                  type="link"
                                fontColor={textColorActive}
                                href="orders/create"
                        >
                                <i className="fa-solid fa-plus mr-1"></i> Add Order
                            </Button>
                        }
                        <Export path="/orders/export" page_title={page_title}/>
                    </div>
                    <div className="flex">
                        <CustomFilter>
                            <Filters filter_inputs={[
                                {name: 'Reference Number', column: 'reference_number'},
                                {name: 'First Name', column: 'first_name'},
                                {name: 'Last Name', column: 'last_name'},
                                {name: 'Delivery Address', column: 'delivery_address'},
                                {name: 'Email Address', column: 'email_address'},
                                {name: 'Contact Details', column: 'contact_details'},
                                {name: 'Downpayment', column: 'has_downpayment'},
                                {name: 'Downpayment Value', column: 'downpayment_value'},
                                {name: 'Financed Amount', column: 'financed_amount'},
                                {name: 'Created By', column: 'created_by'},
                                {name: 'Updated By', column: 'updated_by'},
                                {name: 'Created Date', column: 'created_at'},
                                {name: 'Updated Date', column: 'updated_at'},
                            ]}/>
                        </CustomFilter>
                        <TableSearch queryParams={queryParams} />
                    </div>
                </TopPanel>
                <TableContainer data={orders?.data}>
                    <Thead>
                        <Row>
                            <TableHeader
                                sortable={false}
                                width="md"
                                justify="center"
                            >
                                Action
                            </TableHeader>
                            <TableHeader
                                name="status"
                                queryParams={queryParams}
                                width="md"
                            >
                                Status
                            </TableHeader>
                            <TableHeader
                                name="reference_number"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Reference Number
                            </TableHeader>
                            <TableHeader
                                name="customer_name"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Customer Name
                            </TableHeader>
                            <TableHeader
                                name="delivery_address"
                                queryParams={queryParams}
                                width="xl"
                            >
                                Delivery Address
                            </TableHeader>
                            <TableHeader
                                name="email_address"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Email Address
                            </TableHeader>
                            <TableHeader
                                name="contact_details"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Contact Details
                            </TableHeader>
                            <TableHeader
                                name="is_downpayment"
                                queryParams={queryParams}
                                width="md"
                            >
                                Downpayment
                            </TableHeader>
                            <TableHeader
                                name="downpayment_value"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Downpayment Value
                            </TableHeader>
                            <TableHeader
                                name="financed_amount"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Financed Amount
                            </TableHeader>
                            <TableHeader
                                name="created_by"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Created By
                            </TableHeader>
                            <TableHeader
                                name="updated_by"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Updated By
                            </TableHeader>
                            <TableHeader
                                name="created_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Created Date
                            </TableHeader>
                            <TableHeader
                                name="updated_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Updated Date
                            </TableHeader>
                        </Row>
                    </Thead>
                    <Tbody data={orders?.data}>
                        {orders &&
                            orders?.data.map((item, index) => (
                                <Row key={item.id}>
                                    <RowData center>
                                        {auth.access.isUpdate &&
                                            <RowAction
                                                type="link"
                                                action="edit"
                                                href={`orders/update/${item.id}`}
                                            />
                                        }
                                      <RowAction
                                        type="link"
                                        action="view"
                                        href={`orders/view/${item.id}`}
                                        />
                                    </RowData>
                                    <RowStatus 
                                        color={item?.get_status?.color}
                                    >
                                        {item?.get_status?.name ?? '-'}
                                    </RowStatus>
                                    <RowData>
                                        {item.reference_number ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {`${item.first_name} ${item.last_name}`  ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.delivery_address ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.email_address ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.contact_details ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.has_downpayment}
                                    </RowData>
                                    <RowData>
                                        {item.downpayment_value ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.financed_amount ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.get_created_by?.name ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.get_updated_by?.name ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.created_at ? (moment(item.created_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                    <RowData>
                                        {item.updated_at ? (moment(item.updated_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                </Row>
                            ))}
                    </Tbody>
                </TableContainer>
                <Pagination extendClass={theme} paginate={orders} />
            </ContentPanel>
        </>
    );
};

export default Orders;
