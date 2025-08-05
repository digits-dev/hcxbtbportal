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

const ItemInventories = ({page_title, queryParams, item_inventories}) => {

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

    console.log(item_inventories);

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
                        <Export path="/item_inventories/export" page_title={page_title}/>
                    </div>
                    <div className="flex">
                        <CustomFilter>
                            <Filters filter_inputs={[
                                {name: 'Digits Code', column: 'digits_code'},
                                {name: 'Item Description', column: 'item_description'},
                                {name: 'Stock', column: 'qty'},
                                {name: 'Created Date', column: 'created_at'},
                            ]}/>
                        </CustomFilter>
                        <TableSearch queryParams={queryParams} />
                    </div>
                </TopPanel>
                <TableContainer data={item_inventories?.data}>
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
                                name="digits_code"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Digits Code
                            </TableHeader>
                            <TableHeader
                                sortable={false}
                                queryParams={queryParams}
                                width="2xl"
                            >
                                Item Description
                            </TableHeader>
                            <TableHeader
                                sortable={false}
                                queryParams={queryParams}
                                width="2xl"
                            >
                                Stock
                            </TableHeader>
                            <TableHeader
                                sortable={false}
                                queryParams={queryParams}
                                width="2xl"
                            >
                                Ordered Qty
                            </TableHeader>
                            <TableHeader
                                sortable={false}
                                queryParams={queryParams}
                                width="2xl"
                            >
                                Reservable Qty
                            </TableHeader>
                            <TableHeader
                                name="created_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Created Date
                            </TableHeader>
                        </Row>
                    </Thead>
                    <Tbody data={item_inventories?.data}>
                        {item_inventories &&
                            item_inventories?.data.map((item, index) => (
                                <Row key={item.id}>
                                    <RowData center>
                                        <RowAction
                                            type="link"
                                            action="view"
                                            href={`item_inventories/inventory_details/${item.id}`}
                                        />
                                    </RowData>
                                    <RowData>
                                        {item?.get_item?.digits_code ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item?.get_item?.item_description ?? '-'}
                                    </RowData>
                                     <RowData>
                                        {item.qty ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.reserved_qty ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.qty - item.reserved_qty ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.created_at ? (moment(item.created_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                </Row>
                            ))}
                    </Tbody>
                </TableContainer>
                <Pagination extendClass={theme} paginate={item_inventories} />
            </ContentPanel>
        </>
    );
};

export default ItemInventories;
