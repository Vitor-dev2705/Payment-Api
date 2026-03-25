import streamlit as st
import pandas as pd
import mysql.connector
import plotly.express as px

st.set_page_config(page_title="SmartRoute Analytics", layout="wide")

def get_data():
    try:
        conn = mysql.connector.connect(
            host=st.secrets["DB_HOST"],
            user=st.secrets["DB_USER"],
            password=st.secrets["DB_PASS"],
            database=st.secrets["DB_NAME"]
        )
        df = pd.read_sql("SELECT * FROM transactions", conn)
        conn.close()
        return df
    except:
        data = {
            'gateway': ['pagseguro', 'pagarme', 'pagseguro', 'pagarme'],
            'amount': [100.0, 150.0, 200.0, 50.0],
            'status': ['paid', 'paid', 'paid', 'paid'],
            'created_at': pd.to_datetime(['2026-03-01', '2026-03-02', '2026-03-03', '2026-03-04'])
        }
        return pd.DataFrame(data)

st.title("SmartRoute Payment Dashboard")
st.markdown("Monitoramento de transações e performance de Gateways")

try:
    df = get_data()

    # --- MÉTRICAS PRINCIPAIS ---
    col1, col2, col3 = st.columns(3)
    col1.metric("Total Processado", f"R$ {df['amount'].sum():,.2f}")
    col2.metric("Transações", len(df))
    col3.metric("Gateway Principal", df['gateway'].mode()[0])

    # --- GRÁFICOS ---
    c1, c2 = st.columns(2)

    with c1:
        st.subheader("Vendas por Gateway (Failover)")
        fig_gate = px.pie(df, names='gateway', hole=0.3)
        st.plotly_chart(fig_gate)

    with c2:
        st.subheader("Volume de Vendas no Tempo")
        df['created_at'] = pd.to_datetime(df['created_at'])
        fig_time = px.line(df.sort_values('created_at'), x='created_at', y='amount')
        st.plotly_chart(fig_time)

    # --- TABELA DE DADOS ---
    st.subheader("Últimas Transações")
    st.dataframe(df.sort_values('created_at', ascending=False), use_container_width=True)

except Exception as e:
    st.error(f"Erro ao conectar no banco: {e}")
    st.info("Certifique-se de que o Docker (MySQL) está rodando!")
