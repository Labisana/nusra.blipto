export type ItemType = 'serial' | 'non-serial';

export type TransactionDirection = 'in' | 'out';

export type CustomerOption = {
    id: number;
    nama_pelanggan: string;
    cid: string | null;
    tipe_pelanggan: string;
};

export type ItemOption = {
    id: number;
    sku: string;
    name: string;
    type: ItemType;
    type_label: string;
    unit: string;
    location: string | null;
    min_stock: number;
    stock_in: number;
    stock_out: number;
    remaining: number;
    serials_available?: string[];
};

export type TransactionLine = {
    id: number;
    item_id: number;
    item_name: string;
    item_type: ItemType;
    item_type_label: string;
    quantity: number;
    serials: string[];
};

export type StockTransaction = {
    id: number;
    reference: string;
    direction: TransactionDirection;
    activity: string;
    transaction_date: string;
    note: string | null;
    customer_id: number | null;
    customer: CustomerOption | null;
    user: { id: number; name: string } | null;
    items: TransactionLine[];
};

export type SerialRow = {
    id: number;
    sn: string;
    status: string;
    status_value: 'available' | 'issued';
    item_id: number;
    item_name?: string;
    customer_name?: string | null;
    received_at: string | null;
    issued_at: string | null;
};

export type ReportRow = {
    id: number;
    date: string;
    ref: string;
    activity: string;
    customer: string;
    tipe_pelanggan: string;
    item: string;
    type: string;
    type_value: ItemType;
    qty: number;
    serials: string;
    user: string;
};

export type StockReportRow = {
    id: number;
    sku: string;
    item: string;
    type: ItemType;
    type_label: string;
    in: number;
    out: number;
    remaining: number;
    unit: string;
    location: string | null;
    min_stock: number;
};

export type SelectOption = {
    value: string;
    label: string;
};
